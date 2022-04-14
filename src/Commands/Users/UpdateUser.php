<?php

namespace App\Commands\Users;

use App\Entities\User;
use App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Updates a user')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a user to update'
            )
            ->addOption(
                // Имя опции
                'first-name',
                // Сокращённое имя
                'f',
                // Опция имеет значения
                InputOption::VALUE_OPTIONAL,
                // Описание
                'First name',
            )
            ->addOption(
                'last-name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        // Получаем значения опций
        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');

        // Выходим, если обе опции пусты
        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        // Получаем UUID из аргумента
        $uuid = $input->getArgument('uuid');

        // Получаем пользователя из репозитория
        $user = $this->usersRepository->get($uuid);

        // Берём сохранённое имя,
        // если опция имени пуста
        $first = empty($firstName)
            ? $user->name()->first() : $firstName;
        // Берём сохранённую фамилию,
        // если опция фамилии пуста
        $last = empty($lastName)
            ? $user->name()->last() : $lastName;

        // Создаём новый объект пользователя
        $updatedUser = new User(
            $uuid,
            // Имя пользователя и пароль
            // оставляем без изменений
            $user->username(),
            // Обновлённое имя
            $first,
            $last,
            $user->hashedPassword(),
        );

        // Сохраняем обновлённого пользователя
        $this->usersRepository->save($updatedUser);

        $output->writeln("User updated: $uuid");

        return Command::SUCCESS;
    }
}
