<?php

namespace App\Repositories;

use App\Entities\EntityInterface;
use App\Entities\Post;

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

        $this->logger->info('Post saved as ' . $entity->uuid());
    }

    /**
     * @param string $uuid
     * @return \App\Entities\Post
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
     */
    private function getPost(\PDOStatement $statement, string $field): Post
    {
        $result = $statement->fetch(\PDO::FETCH_OBJ);

        if (false === $result) {
            $this->logger->warning("Cannot find post by author: {$field}");
            return;
        }

        return new Post(
            $result->uuid,
            $result->author_uuid,
            $result->title, 
            $result->text
        );
    }
}
