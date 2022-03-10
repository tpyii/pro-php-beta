<?php

namespace App\Factories;

use App\Entities\Comment;

interface CommentFactoryInterface
{
    /**
     * @return \App\Entities\Comment
     */
    public function create(): Comment;
}
