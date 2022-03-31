<?php

use App\Http\Request;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use App\Http\SuccessfulResponse;
use App\Http\Actions\ActionInterface;
use App\Exceptions\PostNotFoundException;
use App\Repositories\PostRepositoryInterface;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query('uuid');
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->delete($postUuid);

        return new SuccessfulResponse();
    }
}
