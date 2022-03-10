<?php

namespace App\Repositories;

use App\Entities\User;

interface UserRepositoryInterface
{
    /**
     * @param string $username
     * @return \App\Entities\User
     * @throws \App\Exceptions\EntityNotFoundException
     */
    public function getByUsername(string $username): User;
}
