<?php

namespace App\Factories;

use App\Entities\EntityInterface;
use Faker\Factory as Faker;
use Faker\Generator;

abstract class Factory implements FactoryInterface
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * @return \App\Entities\EntityInterface
     */
    abstract public function create(): EntityInterface;
}
