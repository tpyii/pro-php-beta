<?php

namespace App\Factories;

use App\Entities\Post;

interface PostFactoryInterface
{
    /**
     * @return \App\Entities\Post
     */
    public function create(): Post;
}
