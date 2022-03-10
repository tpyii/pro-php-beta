<?php

namespace App\Factories;

use App\Entities\User;

interface UserFactoryInterface
{
    /**
     * @return \App\Entities\User
     */
    public function create(): User;
}
