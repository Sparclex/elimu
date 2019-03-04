<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Institution::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence
    ];
});
