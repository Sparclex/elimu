<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Reagent::class, function (Faker $faker) {
    return [
        'lot' => $faker->unique()->randomNumber(),
        'name' => $faker->word,
        'expires_at' => $faker->date(),
        'assay_id' => function() {
            return factory(App\Models\Assay::class)->create()->id;
        }
    ];
});
