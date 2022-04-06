<?php

use Monolog\Logger;
use App\Http\Request;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use Monolog\Handler\StreamHandler;
use App\Repositories\LikeRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Repositories\CommentRepository;
use App\Http\Actions\Users\FindByUsername;

require_once __DIR__ . '/vendor/autoload.php';

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

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
    $logger->warning($e->getMessage());
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод
    (new ErrorResponse)->send();
    return;
}

$routes = [
    // Добавили ещё один уровень вложенности
    // для отделения маршрутов,
    // применяемых к запросам с разными методами
    'GET' => [
        '/users/show' => new FindByUsername(
            new UserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'POST' => [
        '/users/create' => new CreateUser(
            new UserRepository(
                new PDO('sqlite:' . __DIR__ . '/bloq.sqlite'),
            ),
            $logger
        ),
        // Добавили новый маршрут
        '/posts/create' => new CreatePost(
            new PostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new UserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            $logger
        ),
        // Добавили новый маршрут
        '/posts/comment' => new CreateComment(
            new CommentRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new PostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new UserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        // Добавили новый маршрут
        '/posts/like' => new CreateLike(
            new LikeRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new PostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new UserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'DELETE' => [
        // Добавили новый маршрут
        '/posts' => new DeletePost(
            new PostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ]
];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    $message = 'Not found';
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    $message = 'Not found';
    $logger->notice($message)
    (new ErrorResponse($message))->send();
    return;
}

// Выбираем действие по методу и пути
$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (\Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

$response->send();
