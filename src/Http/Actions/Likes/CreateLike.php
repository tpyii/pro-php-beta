<?php

use Faker\Generator;
use App\Http\Request;
use App\Entities\Like;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use Faker\Factory as Faker;
use App\Http\Auth\AuthException;
use App\Http\SuccessfulResponse;
use App\Http\Actions\ActionInterface;
use App\Exceptions\PostNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\LikeRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Http\Auth\TokenAuthenticationInterface;

class CreateLike implements ActionInterface
{
    protected Generator $faker;

    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
        private TokenAuthenticationInterface $authentication,
    ) {
        $this->faker = Faker::create();
    }

    public function handle(Request $request): Response
    {
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $e) {
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
                $author->uuid(),
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
