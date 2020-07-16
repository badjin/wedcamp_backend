<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SongRequest;
use Faker\Generator as Faker;

$factory->define(SongRequest::class, function (Faker $faker) {
    $user_id = mt_rand(1, 7);
    return [
        'user_id' => $user_id,
        'title' => $faker->text(20),
        'description' => $faker->text(200)
    ];
});
