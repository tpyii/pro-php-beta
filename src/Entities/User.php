<?php

namespace App\Entities;

class User implements UserInterface
{
    public function __construct(
        private string $uuid,
        private string $userName,
        private string $firstName,
        private string $lastName
    ) {}

    /**
     * @return string
     */
    public function uuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function userName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastName;
    }
}
