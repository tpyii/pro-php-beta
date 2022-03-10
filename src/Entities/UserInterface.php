<?php

namespace App\Entities;

interface UserInterface extends EntityInterface
{
    /**
     * @return string
     */
    public function userName(): string;

    /**
     * @return string
     */
    public function firstName(): string;

    /**
     * @return string
     */
    public function lastName(): string;
}
