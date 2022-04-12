<?php

namespace App\Entities;

class User implements UserInterface
{
    public function __construct(
        private string $uuid,
        private string $userName,
        private string $firstName,
        private string $lastName,
        private string $hashedPassword,
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

    /**
     * @return string
     */
    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @return string
     */
    private static function hash(string $password, string $uuid): string
    {
        return hash('sha256', $password . $uuid);
    }

    /**
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid());
    }

    public static function createFrom(
        string $uuid,
        string $username,
        string $firstName,
        string $lastName,
        string $password,
    ): self
    {
        return new self(
            $uuid,
            $username,
            $firstName,
            $lastName,
            self::hash($password, $uuid),
        );
    }
}
