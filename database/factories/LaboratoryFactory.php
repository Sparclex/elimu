<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Laboratory::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence
    ];
});
