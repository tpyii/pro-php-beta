<?php

use App\Http\Request;
use App\Http\Response;
use App\Entities\Comment;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use App\Http\SuccessfulResponse;
use App\Http\Actions\ActionInterface;
use App\Exceptions\PostNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\CommentRepositoryInterface;
use Faker\Factory as Faker;
use Faker\Generator;

class CreateComment implements ActionInterface
{
    protected Generator $faker;

    public function __construct(
        private CommentRepositoryInterface $commentsRepository,
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
    ) {
        $this->faker = Faker::create();
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = $request->jsonBodyField('author_uuid');
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

        $newCommentUuid = $this->faker->uuid();

        try {
            $post = new Comment(
                $newCommentUuid,
                $postUuid,
                $authorUuid,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->commentRepository->save($post);

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}
