<?php

namespace App\Entities;

class Comment implements CommentInterface
{
    public function __construct(
        private string $uuid,
        private string $post_uuid,
        private string $author_uuid,
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
        return $this->post_uuid;
    }

    /**
     * @return string
     */
    public function authorUuid(): string
    {
        return $this->author_uuid;
    }

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }
}
