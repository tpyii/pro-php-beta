<?php

namespace App\Repositories;

use App\Entities\Comment;
use App\Entities\User;
use App\Entities\EntityInterface;
use App\Entities\Post;
use App\Exceptions\MatchException;

class RepositoryFactory implements RepositoryFactoryInterface
{
    public function __construct(
        private \PDO $connection
    ) {}

    /**
     * @param \App\Entities\EntityInterface $entity
     * @return \App\Repositories\EntityRepository
     * @throws \App\Exceptions\MatchException
     */
    public function create(EntityInterface $entity): EntityRepository
    {
        return match ($entity::class) {
            User::class => new UserRepository($this->connection),
            Post::class => new PostRepository($this->connection),
            Comment::class => new CommentRepository($this->connection),
            default => throw new MatchException("Cannot find repository factory for entity")
        };
    }
}
