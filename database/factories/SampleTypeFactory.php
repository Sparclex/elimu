<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\SampleType::class, function (Faker $faker) {
    return [
        'name' => $faker->unique->word
    ];
});
