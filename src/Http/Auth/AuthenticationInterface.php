<?php

namespace App\Http\Auth;

use App\Http\Request;
use App\Entities\User;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}
