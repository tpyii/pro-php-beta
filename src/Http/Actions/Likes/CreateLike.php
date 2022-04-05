<?php

use App\Http\Request;
use App\Http\Response;
use App\Entities\Like;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use App\Http\SuccessfulResponse;
use App\Http\Actions\ActionInterface;
use App\Exceptions\PostNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\LikeRepositoryInterface;
use Faker\Factory as Faker;
use Faker\Generator;

class CreateLike implements ActionInterface
{
    protected Generator $faker;

    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
    ) {
        $this->faker = Faker::create();
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = $request->jsonBodyField('autor_uuid');
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = $request->jsonBodyField('post_uuid');
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newLikeUuid = $this->faker->uuid();

        try {
            $post = new Like(
                $newLikeUuid,
                $authorUuid,
                $postUuid
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likeRepository->save($like);

        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }
}
