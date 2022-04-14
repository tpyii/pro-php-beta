<?php

use Faker\Generator;
use App\Http\Request;
use App\Entities\Post;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use Faker\Factory as Faker;
use Psr\Log\LoggerInterface;
use App\Http\Auth\AuthException;
use App\Http\SuccessfulResponse;
use App\Http\Actions\ActionInterface;
use App\Repositories\PostRepositoryInterface;
use App\Http\Auth\TokenAuthenticationInterface;

class CreatePost implements ActionInterface
{
    protected Generator $faker;

    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    ) {
        $this->faker = Faker::create();
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Create post command started");

        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Генерируем UUID для новой статьи
        $newPostUuid = $this->faker->uuid();

        try {
            // Пытаемся создать объект статьи
            // из данных запроса
            $post = new Post(
                $newPostUuid,
                $author->uuid(),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);

        $this->logger->info("Post created: $newPostUuid");

        // Возвращаем успешный ответ,
        // содержащий UUID новой статьи
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}
