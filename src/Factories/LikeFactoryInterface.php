<?php

namespace App\Factories;

use App\Entities\Like;

interface LikeFactoryInterface
{
    /**
     * @return \App\Entities\Like
     */
    public function create(): Like;
}
