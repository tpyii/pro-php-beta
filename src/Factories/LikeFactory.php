<?php

namespace App\Factories;

use App\Entities\Like;

class LikeFactory extends Factory implements LikeFactoryInterface
{
    public function __construct(
        private $postFactory,
        private $userFactory 
    )
    {
        parent::__construct();
    }
    
    /**
     * @return \App\Entities\Like
     */
    public function create(): Like
    {
        return new Like(
            $this->faker->uuid(),
            $this->postFactory->create()->uuid(),
            $this->userFactory->create()->uuid(),
        );
    }
}
