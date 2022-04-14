<?php

namespace App\Commands;

use Faker\Generator;
use App\Entities\Post;
use App\Entities\User;
use Faker\Factory as Faker;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
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
    ) {
        parent::__construct();

        $this->faker = Faker::create();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int 
    {
        // Создаём десять пользователей
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->username());
        }

        // От имени каждого пользователя
        // создаём по двадцать статей
        foreach ($users as $user) {
            for ($i = 0; $i < 20; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->title());
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
}
