<?php

namespace App\Repositories;

use App\Entities\EntityInterface;
use App\Entities\Like;

class LikeRepository extends EntityRepository implements LikeRepositoryInterface
{
    /**
     * @param \App\Entities\EntityInterface $entity
     * @return void
     */
    public function save(EntityInterface $entity): void
    {
        /**
         * @var Like $entity
         */
        
        $statement = $this->connection->prepare(
            'INSERT INTO likes (uuid, post_uuid, author_uuid)
            VALUES (:uuid, :post_uuid, :author_uuid)'
        );

        $statement->execute([
            ':uuid' => $entity->uuid(),
            ':post_uuid' => $entity->postUuid(),
            ':author_uuid' => $entity->authorUuid(),
        ]);

        $this->logger->info('Like saved as ' . $entity->uuid());
    }

    /**
     * @param string $uuid
     * @return \App\Entities\Like
     */
    public function get(string $uuid): Like
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);

        return $this->getLike($statement, $uuid);
    }

    /**
     * @param string $username
     * @return array
     */
    public function getByPostUuid(string $postUuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid'
        );

        $statement->execute([
            ':post_uuid' => $postUuid,
        ]);

        return $this->getLikes($statement, $postUuid);
    }

    /**
     * @param \PDOStatement $statement
     * @param string $field
     * @return \App\Entities\Like
     */
    private function getLike(\PDOStatement $statement, string $field): Like
    {
        $result = $statement->fetch(\PDO::FETCH_OBJ);

        if (false === $result) {
            $this->logger->warning("Cannot find likes by post: {$field}");
            return;
        }

        return new Like(
            $result->uuid,
            $result->post_uuid,
            $result->author_uuid, 
        );
    }

    /**
     * @param \PDOStatement $statement
     * @param string $field
     * @return array
     */
    private function getLikes(\PDOStatement $statement, string $field): array
    {
        while ($row = $statement->fetch(\PDO::FETCH_OBJ)) {
            $result[] = new Like(
                $row->uuid,
                $row->post_uuid,
                $row->author_uuid, 
            );
        }

        if (false === $result) {
            $this->logger->warning("Cannot find like: {$field}");
            return;
        }

        return $result;
    }
}
