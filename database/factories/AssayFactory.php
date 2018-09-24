<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Assay::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
        'sop' => $faker->word
    ];
});
