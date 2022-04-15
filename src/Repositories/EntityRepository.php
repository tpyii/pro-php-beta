<?php

namespace App\Repositories;

use Psr\Log\LoggerInterface;
use App\Entities\EntityInterface;

abstract class EntityRepository implements EntityRepositoryInterface
{
    public function __construct(
        protected \PDO $connection,
        private LoggerInterface $logger,
    ) {}
    
    /**
     * @param \App\Entities\EntityInterface $entity
     * @return void
     */
    abstract public function save(EntityInterface $entity): void;

    /**
     * @param string $uuid
     * @return \App\Entities\EntityInterface
     * @throws \App\Exceptions\EntityNotFoundException
     */
    abstract public function get(string $uuid): EntityInterface;

    /**
     * @param string $uuid
     * @return void
     * @throws \App\Exceptions\EntityNotFoundException
     */
    abstract public function delete(string $uuid): void;
}
