<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\PrimerMix::class, function (Faker $faker) {
    return [
        'reagent_id' => function () {
            return factory(\App\Models\Reagent::class)->create()->id;
        },
        'creator_id' => function () {
            return factory(\App\Models\Person::class)->create()->id;
        },
        'name' => $faker->unique()->word,
        'expires_at' => $faker->date,
        'volume' => $faker->randomNumber(2)
    ];
});
