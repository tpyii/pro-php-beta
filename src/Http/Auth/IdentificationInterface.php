<?php

namespace App\Http\Auth;

use App\Http\Request;
use App\Entities\User;

interface IdentificationInterface
{
    public function user(Request $request): User;
}
