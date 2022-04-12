<?php

namespace App\Http\Auth;

use App\Http\Request;
use App\Entities\User;
use DateTimeImmutable;
use App\Http\HttpException;
use App\Exceptions\EntityNotFoundException;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\AuthTokensRepositoryInterface;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private UserRepositoryInterface $usersRepository,
    ) {
    }

    public function user(Request $request): User
    {
        // Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        // Отрезаем префикс Bearer
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        // Ищем токен в репозитории
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (EntityNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        // Проверяем срок годности токена
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        // Получаем UUID пользователя из токена
        $userUuid = $authToken->userUuid();

        // Ищем и возвращаем пользователя
        return $this->usersRepository->get($userUuid);
    }
}
