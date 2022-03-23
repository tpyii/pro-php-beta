<?php

namespace App\Entities;

interface CommentInterface extends EntityInterface
{
    /**
     * @return string
     */
    public function postUuid(): string;

    /**
     * @return string
     */
    public function authorUuid(): string;

    /**
     * @return string
     */
    public function text(): string;
}
