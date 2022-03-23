<?php

namespace App\Factories;

use App\Entities\EntityInterface;
use App\Exceptions\MatchException;
use App\Traits\Singletone;

class EntityFactory implements EntityFactoryInterface
{
    use Singletone;

    private static UserFactoryInterface $userFactory;
    private static PostFactoryInterface $postFactory;
    private static CommentFactoryInterface $commentFactory;

    private function __construct()
    {
        self::$userFactory = new UserFactory;
        self::$postFactory = new PostFactory(self::$userFactory);
        self::$commentFactory = new CommentFactory(self::$userFactory, self::$postFactory);
    }

    /**
     * @param string $type
     * @return \App\Entities\EntityInterface
     * @throws \App\Exceptions\MatchException
     */
    public function create(string $type): EntityInterface
    {
        return match ($type) {
            'user' => self::$userFactory->create(),
            'post' => self::$postFactory->create(),
            'comment' => self::$commentFactory->create(),
            default => throw new MatchException("Cannot find entity factory: {$type}")
        };
    }
}
