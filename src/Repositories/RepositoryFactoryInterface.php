<?php

namespace App\Repositories;

use App\Entities\EntityInterface;

interface RepositoryFactoryInterface 
{
    /**
     * @param \App\Entities\EntityInterface $entity
     * @return \App\Repositories\EntityRepository
     * @throws \App\Exceptions\MatchException
     */
    public function create(EntityInterface $entity): EntityRepository;
}
