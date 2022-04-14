<?php

namespace App\Repositories;

use DateTimeImmutable;
use App\Entities\AuthToken;

class AuthTokensRepository implements AuthTokensRepositoryInterface
{
    public function __construct(
        private \PDO $connection
    ) {
    }

    /**
     * @param \App\Entities\AuthToken $entity
     * @return void
     */
    public function save(AuthToken $entity): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO tokens (token, user_uuid, expires_on)
            VALUES (:token, :user_uuid, :expires_on)
            ON CONFLICT (token) DO UPDATE SET expires_on = :expires_on'
        );

        $statement->execute([
            ':token' => $entity->token(),
            ':user_uuid' => $entity->userUuid(),
            ':expires_on' => $entity->expiresOn(),
        ]);
        
        $this->logger->info('Token saved as ' . $entity->token());
    }

    /**
     * @param string $token
     * @return \App\Entities\AuthToken
     */
    public function get(string $token): AuthToken
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM tokens WHERE token = :token'
        );

        $statement->execute([
            ':token' => $token,
        ]);

        return $this->getToken($statement, $token);
    }

    /**
     * @param string $uuid
     * @return \App\Entities\AuthToken
     */
    public function getByUserUuid(string $uuid): AuthToken
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM tokens WHERE user_uuid = :uuid'
        );

        $statement->execute([
            ':user_uuid' => $uuid,
        ]);

        return $this->getToken($statement, $uuid);
    }

    /**
     * @param \PDOStatement $statement
     * @param string $field
     * @return \App\Entities\AuthToken
     */
    private function getToken(\PDOStatement $statement, string $field): AuthToken
    {
        $result = $statement->fetch(\PDO::FETCH_OBJ);

        if (false === $result) {
            $this->logger->warning("Cannot find token: {$field}");
            return;
        }

        return new AuthToken(
            $result->token,
            $result->user_uuid, 
            new DateTimeImmutable($result->expires_on),
        );
    }
}
