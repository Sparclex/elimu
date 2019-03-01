<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Reagent::class, function (Faker $faker) {
    return [
        'lot' => $faker->unique()->randomNumber(),
        'name' => $faker->unique()->word,
        'expires_at' => $faker->date(),
        'study_id' => function () {
            return \Illuminate\Support\Facades\Auth::user()->study_id ?? factory(\App\Models\Study::class)->create()->id;
        },
    ];
});
