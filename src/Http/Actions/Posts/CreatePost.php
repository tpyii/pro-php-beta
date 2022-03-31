<?php

use App\Http\Request;
use App\Entities\Post;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use App\Http\SuccessfulResponse;
use App\Http\Actions\ActionInterface;
use App\Exceptions\UserNotFoundException;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Faker\Factory as Faker;
use Faker\Generator;

class CreatePost implements ActionInterface
{
    protected Generator $faker;

    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
    ) {
        $this->faker = Faker::create();
    }

    public function handle(Request $request): Response
    {
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
            return new ErrorResponse($e->getMessage());
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

        // Возвращаем успешный ответ,
        // содержащий UUID новой статьи
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}
