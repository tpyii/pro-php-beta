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
use App\Exceptions\EntityNotFoundException;
use App\Http\Auth\TokenAuthenticationInterface;
use App\Repositories\AuthTokensRepositoryInterface;

class LogOut implements ActionInterface
{
    public function __construct(
        private TokenAuthenticationInterface $authentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $authToken = $this->authTokensRepository->getByUserUuid($user->uuid());
        } catch (EntityNotFoundException) {
            throw new AuthException("Bad token by user: [$user->uuid()]");
        }

        $authToken = new AuthToken(
            $authToken->token(),
            $authToken->userUuid(),
            new DateTimeImmutable(),
        );

        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse();
    }
}
