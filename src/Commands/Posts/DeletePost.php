<?php

namespace App\Commands\Posts;

use App\Exceptions\EntityNotFoundException;
use Faker\Generator;
use Faker\Factory as Faker;
use App\Repositories\PostRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeletePost extends Command
{
    protected Generator $faker;

    public function __construct(
        // Внедряем репозиторий пользователей
        private PostRepositoryInterface $postsRepository,
    ) {
        // Вызываем родительский конструктор
        parent::__construct();

        $this->faker = Faker::create();
    }

    // Конфигурируем команду
    protected function configure(): void
    {
        $this
            ->setName('posts:delete')
            ->setDescription('Deletes a post')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a post to delete'
            )

            // Добавили опцию
            ->addOption(
                // Имя опции
                'check-existence',
                // Сокращённое имя
                'c',
                // Опция не имеет значения
                InputOption::VALUE_NONE,
                // Описание
                'Check if post actually exists',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $question = new ConfirmationQuestion(
            // Вопрос для подтверждения
            'Delete post [Y/n]? ',
            // По умолчанию не удалять
            false
        );
 
        // Ожидаем подтверждения
        if (!$this->getHelper('question')
            ->ask($input, $output, $question)
        ) {
            // Выходим, если удаление не подтверждено
            return Command::SUCCESS;
        }
 
        // Получаем UUID статьи
        $uuid = $input->getArgument('uuid');

        // Если опция проверки существования статьи установлена
        if ($input->getOption('check-existence')) {
            try {
                // Пытаемся получить статью
                $this->postsRepository->get($uuid);
            } catch (EntityNotFoundException $e) {
                // Выходим, если статья не найдена
                $output->writeln($e->getMessage());
                return Command::FAILURE;
            }
        }

        // Удаляем статью из репозитория
        $this->postsRepository->delete($uuid);

        $output->writeln("Post $uuid deleted");

        return Command::SUCCESS;
    }
}
