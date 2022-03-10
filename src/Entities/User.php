<?php

namespace App\Entities;

class User implements UserInterface
{
    public function __construct(
        private string $uuid,
        private string $username,
        private string $first_name,
        private string $last_name
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
        return $this->username;
    }

    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->last_name;
    }
}
