<?php

use Faker\Generator;
use App\Http\Request;
use App\Entities\Post;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use Faker\Factory as Faker;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Http\Actions\ActionInterface;
use App\Exceptions\UserNotFoundException;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepositoryInterface;

class CreatePost implements ActionInterface
{
    protected Generator $faker;

    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    ) {
        $this->faker = Faker::create();
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Create post command started");

        // Пытаемся создать UUID пользователя из данных запроса
        try {
            $authorUuid = $request->jsonBodyField('author_uuid');
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти пользователя в репозитории
        try {
            $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            $this->logger->warning("User not found by id: $authorUuid");
            return;
        }

        // Генерируем UUID для новой статьи
        $newPostUuid = $this->faker->uuid();

        try {
            // Пытаемся создать объект статьи
            // из данных запроса
            $post = new Post(
                $newPostUuid,
                $authorUuid,
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
