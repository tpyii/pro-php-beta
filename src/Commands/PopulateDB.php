<?php

namespace App\Commands;

use App\Entities\Comment;
use Faker\Generator;
use App\Entities\Post;
use App\Entities\User;
use App\Repositories\CommentRepositoryInterface;
use Faker\Factory as Faker;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    private Generator $faker;
    
    // Внедряем генератор тестовых данных и
    // репозитории пользователей и статей
    public function __construct(
        private UserRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postsRepository,
        private CommentRepositoryInterface $commentsRepository,
    ) {
        parent::__construct();

        $this->faker = Faker::create();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption('users-number', 'u', InputOption::VALUE_OPTIONAL, 'Count users')
            ->addOption('article-number', 'a', InputOption::VALUE_OPTIONAL, 'Count articles');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int 
    {
        // Получаем значения опций
        (int)$usersCount = $input->getOption('users-number');
        (int)$postsCount = $input->getOption('article-number');

        // Создаём десять пользователей
        $users = [];
        for ($i = 0; $i < $usersCount ?? 10; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->username());
        }

        // От имени каждого пользователя
        // создаём по двадцать статей
        $posts = [];
        foreach ($users as $user) {
            for ($i = 0; $i < $postsCount ?? 20; $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->title());
            }

            for ($i = 0; $i < count($posts) ?? 20; $i++) {
                $comment = $this->createFakeComment($post, $user);
                $output->writeln('Comment created: ' . $comment->text());
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
            $this->faker->uuid(),
            // Генерируем имя пользователя
            $this->faker->userName(),
            // Генерируем имя
            $this->faker->firstName(),
            // Генерируем фамилию
            $this->faker->lastName(),
            // Генерируем пароль
            $this->faker->password(),
        );

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);

        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            $this->faker->uuid(),
            $author->uuid(),
            // Генерируем предложение
            $this->faker->sentence(),
            // Генерируем текст
            $this->faker->text()
        );

        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);

        return $post;
    }

    private function createFakeComment(Post $post, User $author): Comment
    {
        $comment = new Comment(
            $this->faker->uuid(),
            $post->uuid(),
            $author->uuid(),
            $this->faker->text()
        );

        $this->commentsRepository->save($comment);

        return $comment;
    }
}
