<?php

require_once 'vendor/autoload.php';

use App\Comment\Comment;
use App\Post\Post;
use App\User\User;

$faker = Faker\Factory::create();

switch ($argv[1]) {
    case 'user':
        echo new User(
            $faker->unixTime(),
            $faker->firstName(),
            $faker->lastName()
        ) . PHP_EOL;
        break;

    case 'post':
        $id = $faker->unixTime();
        echo new Post(
            $id,
            $id,
            $faker->paragraph(),
            $faker->text()
        ) . PHP_EOL;
        break;
        
    case 'comment':
        $id = $faker->unixTime();
        echo new Comment(
            $id,
            $id,
            $id,
            $faker->sentence()
        ) . PHP_EOL;
        break;
}
