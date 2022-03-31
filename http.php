<?php

use App\Http\Request;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Http\Actions\Users\FindByUsername;
use App\Repositories\CommentRepository;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
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
        // Добавили новый маршрут
        '/posts/create' => new CreatePost(
            new PostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new UserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
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
    (new ErrorResponse('Not found'))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Выбираем действие по методу и пути
$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (\Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
