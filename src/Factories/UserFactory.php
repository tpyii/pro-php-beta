<?php

namespace App\Factories;

use App\Entities\User;

class UserFactory extends Factory implements UserFactoryInterface
{
    /**
     * @return \App\Entities\User
     */
    public function create(): User
    {
        return new User(
            $this->faker->uuid(),
            $this->faker->userName(),
            $this->faker->firstName(),
            $this->faker->lastName()
        );
    }
}
