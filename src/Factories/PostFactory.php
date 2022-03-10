<?php

namespace App\Factories;

use App\Entities\Post;

class PostFactory extends Factory implements PostFactoryInterface
{
    public function __construct(private UserFactory $userFactory)
    {
        parent::__construct();
    }
    
    /**
     * @return \App\Entities\Post
     */
    public function create(): Post
    {
        return new Post(
            $this->faker->uuid(),
            $this->userFactory->create()->uuid(),
            $this->faker->sentence(),
            $this->faker->text()
        );
    }
}
