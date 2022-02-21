<?php

namespace App\Comment;

class Comment
{
    public function __construct(
        private int $id,
        private int $user_id,
        private int $post_id,
        private string $text
    ) {  
    }

    public function __toString()
    {
        return $this->text;
    }
}
