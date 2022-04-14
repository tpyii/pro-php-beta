<?php

namespace App\Http\Actions\Auth;

use App\Http\Request;
use App\Http\Response;
use DateTimeImmutable;
use App\Entities\AuthToken;
use App\Http\ErrorResponse;
use App\Http\Auth\AuthException;
use App\Http\SuccessfulResponse;
use App\Http\Actions\ActionInterface;
use App\Http\Auth\PasswordAuthenticationInterface;
use App\Repositories\AuthTokensRepositoryInterface;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->uuid(),
            (new DateTimeImmutable())->modify('+1 day'),
        );

        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => (string)$authToken,
        ]);
    }
}
