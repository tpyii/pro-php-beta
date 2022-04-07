<?php

use Faker\Generator;
use App\Http\Request;
use App\Entities\User;
use App\Http\Response;
use App\Http\ErrorResponse;
use App\Http\HttpException;
use Faker\Factory as Faker;
use Psr\Log\LoggerInterface;
use App\Http\SuccessfulResponse;
use App\Http\Actions\ActionInterface;
use App\Repositories\UserRepositoryInterface;

class CreateUser implements ActionInterface
{
    protected Generator $faker;

    public function __construct(
        private UserRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    ) {
        $this->faker = Faker::create();
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Create user command started");

        try {
            $userName = $request->jsonBodyField('user_name');
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        if ($this->usersRepository->getByUsername($userName)) {
            $this->logger->warning("User already exists: $userName");
            return;
        }

        $uuid = $this->faker->uuid();

        try {
            $user = new User(
                $uuid,
                $userName,
                $request->jsonBodyField('first_name'),
                $request->jsonBodyField('last_name'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->userRepository->save($user);

        $this->logger->info("User created: $uuid");

        return new SuccessfulResponse([
            'uuid' => (string)$uuid,
        ]);
    }
}
