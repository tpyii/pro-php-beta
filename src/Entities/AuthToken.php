<?php

namespace App\Entities;

use DateTimeImmutable;

class AuthToken implements AuthTokenInterface
{
    public function __construct(
        private string $token,
        private string $userUuid,
        private DateTimeImmutable $expiresOn
    ) {
    }

    public function token(): string
    {
        return $this->token;
    }

    public function userUuid(): string
    {
        return $this->userUuid;
    }

    public function expiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }
}
