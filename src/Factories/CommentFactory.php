<?php

namespace App\Factories;

use App\Entities\Comment;

class CommentFactory extends Factory implements CommentFactoryInterface
{
    public function __construct(
        private $userFactory, 
        private $postFactory
    )
    {
        parent::__construct();
    }

    /**
     * @return \App\Entities\Comment
     */
    public function create(): Comment
    {
        return new Comment(
            $this->faker->uuid(),
            $this->userFactory->create()->uuid(),
            $this->postFactory->create()->uuid(),
            $this->faker->text()
        );
    }
}
