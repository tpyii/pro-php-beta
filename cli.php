<?php

require_once 'vendor/autoload.php';

use Monolog\Logger;
use App\Commands\Posts\DeletePost;
use App\Commands\Users\CreateUser;
use App\Commands\Users\UpdateUser;
use Monolog\Handler\StreamHandler;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Symfony\Component\Console\Application;

$connector = new PDO('sqlite:' . __DIR__ . '/database.sqlite');

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

// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    new CreateUser(
        new UserRepository($connector, $logger)
    ),
    new UpdateUser(
        new UserRepository($connector, $logger)
    ),
    new DeletePost(
        new PostRepository($connector, $logger)
    ),
];

foreach ($commandsClasses as $commandClass) {
    // Добавляем команду к приложению
    $application->add($commandClass);
}

// Запускаем приложение
$application->run();
