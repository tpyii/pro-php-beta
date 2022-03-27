<?php

namespace App\UnitTests;

use PHPUnit\Framework\TestCase;
use App\Connectors\SqliteConnector;
use App\Repositories\PostRepository;
use App\Exceptions\EntityNotFoundException;

final class PostRepositoryTest extends TestCase
{
    public function testItFoundPostFromRepositoryByUuid(): void
    {
        $uuid = '47b0dae4-c0fd-378c-808b-17362c70a1ba';   
        
        $this->repository = new PostRepository(
            SqliteConnector::getInstance()->getConnection()
        );

        $post = $this->repository->get($uuid);

        $this->assertEquals($uuid, $post->uuid());
    }

    public function testItThrowsAnExceptionWhenPostIsAbsent(): void
    {
        $uuid = '123';   
        
        $this->repository = new PostRepository(
            SqliteConnector::getInstance()->getConnection()
        );

        $this->expectException(EntityNotFoundException::class);

        $this->expectExceptionMessage("Cannot find post by author: {$uuid}");

        $this->repository->get($uuid);
    }
}
