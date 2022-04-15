<?php

namespace App\Commands\Comments;

use Faker\Generator;
use App\Entities\Comment;
use Faker\Factory as Faker;
use App\Exceptions\PostNotFoundException;
use App\Exceptions\UserNotFoundException;
use Symfony\Component\Console\Command\Command;
use App\Repositories\CommentRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateComment extends Command
{
    protected Generator $faker;

    public function __construct(
        private CommentRepositoryInterface $commentsRepository,
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
    ) {
        parent::__construct();

        $this->faker = Faker::create();
    }

    protected function configure(): void
    {
        $this
            ->setName('comments:create')
            ->setDescription('Creates new comment')
            ->addArgument('post_uuid', InputArgument::REQUIRED, 'Post id')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('text', InputArgument::REQUIRED, 'Text');
    }

    protected function execute(
        InputInterface $input, 
        OutputInterface $output,
    ): int
    {
        $output->writeln('Create comment command started');

        $post_uuid = $input->getArgument('post_uuid');

        if ($this->postExists($post_uuid)) {
            $output->writeln("Post already exists: $post_uuid");
            return Command::FAILURE;
        }

        $author_uuid = $input->getArgument('author_uuid');

        if ($this->userExists($author_uuid)) {
            $output->writeln("User already exists: $author_uuid");
            return Command::FAILURE;
        }

        $comment = new Comment(
            $this->faker->uuid(),
            $input->getArgument('post_uuid'),
            $input->getArgument('author_uuid'),
            $input->getArgument('text'),
        );

        $this->commentsRepository->save($comment);

        $output->writeln('Comment created: ' . $comment->uuid());

        return Command::SUCCESS;
    }

    private function postExists(string $uuid): bool
    {
        try {
            $this->postsRepository->get($uuid);
        } catch (PostNotFoundException) {
            return false;
        }
        return true;
    }

    private function userExists(string $uuid): bool
    {
        try {
            $this->usersRepository->get($uuid);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
