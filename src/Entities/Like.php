<?php

namespace App\Entities;

class Like implements LikeInterface
{
    public function __construct(
        private string $uuid,
        private string $postUuid,
        private string $authorUuid,
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
    public function postUuid(): string
    {
        return $this->postUuid;
    }

    /**
     * @return string
     */
    public function authorUuid(): string
    {
        return $this->authorUuid;
    }
}
