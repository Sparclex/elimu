<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Experiment::class, function (Faker $faker) {
    return [
        'study_id' => function () {
            return factory(\App\Models\Study::class)->create()->id;
        },
        'reagent_id' => function () {
            return factory(\App\Models\Reagent::class)->create()->id;
        },
        'requested_at' => $faker->dateTime,
        'requester_id' => function () {
            return factory(\App\User::class)->create()->id;
        },
        'comment' => $faker->paragraph
    ];
});
