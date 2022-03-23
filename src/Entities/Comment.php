<?php

namespace App\Entities;

class Comment implements CommentInterface
{
    public function __construct(
        private string $uuid,
        private string $postUuid,
        private string $authorUuid,
        private string $text
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

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }
}
