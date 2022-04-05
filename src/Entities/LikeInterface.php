<?php

namespace App\Entities;

interface LikeInterface extends EntityInterface
{
    /**
     * @return string
     */
    public function postUuid(): string;

    /**
     * @return string
     */
    public function authorUuid(): string;
}
