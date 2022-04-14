<?php

namespace App\Http\Auth;

use App\Http\Request;
use App\Entities\User;
use App\Http\HttpException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    ) {
    }

    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('user_name');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }
        
        return $user;
    }
}
