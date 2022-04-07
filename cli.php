<?php

require_once 'vendor/autoload.php';

use Monolog\Logger;
use App\Factories\EntityFactory;
use Monolog\Handler\StreamHandler;
use App\Connectors\SqliteConnector;
use App\Repositories\RepositoryFactory;

$logger = (new Logger('blog'))
    ->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
    ->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.error.log',
        level: Logger::ERROR,
        bubble: false,
    ))
    ->pushHandler(new StreamHandler(
        "php://stdout"
    ));

$entity = EntityFactory::getInstance()->create('like');

$factory = new RepositoryFactory(
    SqliteConnector::getInstance()->getConnection(),
    $logger,
);

$entityRepository = $factory->create($entity);

try {
    $entityRepository->save($entity);
    print_r($entityRepository->get($entity->uuid()));
} catch (\Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
}
