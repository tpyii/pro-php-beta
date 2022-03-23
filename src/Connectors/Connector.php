<?php

namespace App\Connectors;

use App\Traits\Singletone;

abstract class Connector implements ConnectorInterface
{
    use Singletone;

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return new \PDO($this->getDsn());
    }

    /**
     * @return string
     */
    abstract public function getDsn(): string;
}
