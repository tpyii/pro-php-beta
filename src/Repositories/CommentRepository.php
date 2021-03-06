<?php

namespace App\Repositories;

use App\Entities\EntityInterface;
use App\Entities\Comment;

class CommentRepository extends EntityRepository implements CommentRepositoryInterface
{
    /**
     * @param \App\Entities\EntityInterface $entity
     * @return void
     */
    public function save(EntityInterface $entity): void
    {
        /**
         * @var Comment $entity
         */
        
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
            VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => $entity->uuid(),
            ':post_uuid' => $entity->postUuid(),
            ':author_uuid' => $entity->authorUuid(),
            ':text' => $entity->text(),
        ]);
        
        $this->logger->info('Comment saved as ' . $entity->uuid());
    }

    /**
     * @param string $uuid
     * @return \App\Entities\Comment
     */
    public function get(string $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }

    /**
     * @param \PDOStatement $statement
     * @param string $field
     * @return \App\Entities\Comment
     */
    private function getComment(\PDOStatement $statement, string $field): Comment
    {
        $result = $statement->fetch(\PDO::FETCH_OBJ);

        if (false === $result) {
            $this->logger->warning("Cannot find comment: {$field}");
            return;
        }

        return new Comment(
            $result->uuid,
            $result->post_uuid,
            $result->author_uuid, 
            $result->text
        );
    }
}
