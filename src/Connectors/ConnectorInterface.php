<?php

namespace App\Connectors;

interface ConnectorInterface
{
    /**
     * @return \PDO
     */
    public function getConnection(): \PDO;
}
