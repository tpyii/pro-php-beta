<?php

namespace App\Http\Auth;

use App\Http\Request;
use App\Entities\User;
use App\Http\HttpException;
use InvalidArgumentException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;

class JsonBodyUsernameIdentification implements IdentificationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    ) {
    }

    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('user_name');
        } catch (HttpException | InvalidArgumentException $e) {
            return new AuthException($e->getMessage());
        }

        try {
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}
