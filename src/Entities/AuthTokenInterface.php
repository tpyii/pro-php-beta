<?php

namespace App\Entities;

interface AuthTokenInterface
{
    /**
     * @return string
     */
    public function token(): string;

    /**
     * @return string
     */
    public function userUuid(): string;

    /**
     * @return \DateTimeImmutable
     */
    public function expiresOn(): \DateTimeImmutable;
}
