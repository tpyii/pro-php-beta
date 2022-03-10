<?php

namespace App\Entities;

interface PostInterface extends EntityInterface
{
    /**
     * @return string
     */
    public function authorUuid(): string;

    /**
     * @return string
     */
    public function title(): string;

    /**
     * @return string
     */
    public function text(): string;
}
