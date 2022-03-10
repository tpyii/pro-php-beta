<?php

require_once 'vendor/autoload.php';

use App\Connectors\SqliteConnector;
use App\Factories\EntityFactory;
use App\Repositories\RepositoryFactory;

$entity = EntityFactory::getInstance()->create('user');

$factory = new RepositoryFactory(
    SqliteConnector::getInstance()->getConnection()
);

$entityRepository = $factory->create($entity);

try {
    $entityRepository->save($entity);
    print_r($entityRepository->get($entity->uuid()));
} catch (\Exception $e) {
    echo $e->getMessage();
}
