<?php

namespace App\Connectors;

class SqliteConnector extends Connector
{
    /**
     * @return string
     */
    public function getDsn(): string
    {
        return 'sqlite:' . __DIR__ . '/../../database.sqlite';
    }
}
