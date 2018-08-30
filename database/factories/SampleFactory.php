<?php

use Faker\Generator as Faker;

$factory->define(App\Sample::class, function (Faker $faker) {
    return [
        'brady_number' => $faker->randomNumber(5),
        'subject_id' => $faker->md5,
        'collected_at' => $faker->dateTimeThisYear,
        'received_at' => $faker->dateTimeThisYear,
        'visit' => $faker->randomElement(['V1', 'V2', 'V2+14', 'CH+14']),
        'comment' => function () use ($faker) {
            return rand(0, 1) == 1 ? $faker->text : null;
        },
        'receiver_id' => function () {
            return factory(App\Person::class)->create()->id;
        },
        'deliverer_id' => function () {
            return factory(App\Person::class)->create()->id;
        },
        'study_id' => function () {
            return factory(App\Study::class)->create()->id;
        },
        'condition' => function () use ($faker) {
            return rand(0, 1) == 1 ? $faker->text : null;
        },
    ];
});
