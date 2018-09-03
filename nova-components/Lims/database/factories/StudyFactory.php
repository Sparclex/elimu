<?php

use Faker\Generator as Faker;

$factory->define(\Sparclex\Lims\Models\Study::class, function (Faker $faker) {
    return [
        'study_id' => $faker->unique->randomNumber(5),
        'name' => $faker->unique->word
    ];
});
