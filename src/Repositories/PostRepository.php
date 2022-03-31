<?php

namespace App\Repositories;

use App\Entities\EntityInterface;
use App\Entities\Post;
use App\Exceptions\EntityNotFoundException;

class PostRepository extends EntityRepository implements PostRepositoryInterface
{
    /**
     * @param \App\Entities\EntityInterface $entity
     * @return void
     */
    public function save(EntityInterface $entity): void
    {
        /**
         * @var Post $entity
         */
        
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text)
            VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => $entity->uuid(),
            ':author_uuid' => $entity->authorUuid(),
            ':title' => $entity->title(),
            ':text' => $entity->text(),
        ]);
    }

    /**
     * @param string $uuid
     * @return \App\Entities\Post
     * @throws \App\Exceptions\EntityNotFoundException
     */
    public function get(string $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    /**
     * @param string $uuid
     * @return void
     */
    public function delete(string $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);
    }

    /**
     * @param \PDOStatement $statement
     * @param string $field
     * @return \App\Entities\Post
     * @throws \App\Exceptions\EntityNotFoundException
     */
    private function getPost(\PDOStatement $statement, string $field): Post
    {
        $result = $statement->fetch(\PDO::FETCH_OBJ);

        if (false === $result) {
            throw new EntityNotFoundException("Cannot find post by author: {$field}");
        }

        return new Post(
            $result->uuid,
            $result->author_uuid,
            $result->title, 
            $result->text
        );
    }
}
