<?php

namespace App\Repositories;

interface LikeRepositoryInterface
{
    /**
     * @param string $postUuid
     * @return array
     * @throws \App\Exceptions\EntityNotFoundException
     */
    public function getByPostUuid(string $postUuid): array;
}
