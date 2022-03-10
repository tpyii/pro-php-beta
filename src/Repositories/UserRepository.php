<?php

namespace App\Repositories;

use App\Entities\EntityInterface;
use App\Entities\User;
use App\Exceptions\EntityNotFoundException;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * @param \App\Entities\EntityInterface $entity
     * @return void
     */
    public function save(EntityInterface $entity): void
    {
        /**
         * @var User $entity
         */
        
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name)
            VALUES (:uuid, :username, :first_name, :last_name)'
        );

        $statement->execute([
            ':uuid' => $entity->uuid(),
            ':username' => $entity->userName(),
            ':first_name' => $entity->firstName(),
            ':last_name' => $entity->lastName(),
        ]);
    }

    /**
     * @param string $uuid
     * @return \App\Entities\User
     * @throws \App\Exceptions\EntityNotFoundException
     */
    public function get(string $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);

        return $this->getUser($statement, $uuid);
    }

    /**
     * @param string $username
     * @return \App\Entities\User
     * @throws \App\Exceptions\EntityNotFoundException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([
            ':username' => $username,
        ]);

        return $this->getUser($statement, $username);
    }

    /**
     * @param \PDOStatement $statement
     * @param string $field
     * @return \App\Entities\User
     * @throws \App\Exceptions\EntityNotFoundException
     */
    private function getUser(\PDOStatement $statement, string $field): User
    {
        $result = $statement->fetch(\PDO::FETCH_OBJ);

        if (false === $result) {
            throw new EntityNotFoundException("Cannot find user: {$field}");
        }

        return new User(
            $result->uuid,
            $result->username,
            $result->first_name, 
            $result->last_name
        );
    }
}
