<?php

namespace App\Entities;

class Post implements PostInterface
{
    public function __construct(
        private string $uuid,
        private string $authorUuid,
        private string $title,
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
    public function authorUuid(): string
    {
        return $this->authorUuid;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }
}
