<?php

namespace App\Post;

class Post
{
    public function __construct(
        private int $id,
        private int $user_id,
        private string $title,
        private string $text
    ) {
    }

    public function __toString()
    {
        return $this->text;
    }
}
