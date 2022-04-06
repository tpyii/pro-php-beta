<?php

namespace App\Repositories;

use App\Entities\Post;
use App\Entities\User;
use App\Entities\Comment;
use Psr\Log\LoggerInterface;
use App\Entities\EntityInterface;
use App\Exceptions\MatchException;

class RepositoryFactory implements RepositoryFactoryInterface
{
    public function __construct(
        private \PDO $connection,
        private LoggerInterface $logger,
    ) {}

    /**
     * @param \App\Entities\EntityInterface $entity
     * @return \App\Repositories\EntityRepository
     * @throws \App\Exceptions\MatchException
     */
    public function create(EntityInterface $entity): EntityRepository
    {
        return match ($entity::class) {
            User::class => new UserRepository($this->connection, $this->logger),
            Post::class => new PostRepository($this->connection, $this->logger),
            Comment::class => new CommentRepository($this->connection, $this->logger),
            Like::class => new LikeRepository($this->connection, $this->logger),
            default => throw new MatchException("Cannot find repository factory for entity")
        };
    }
}
