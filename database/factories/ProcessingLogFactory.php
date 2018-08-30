<?php

use Faker\Generator as Faker;

$factory->define(App\ProcessingLog::class, function (Faker $faker) {
    return [
        'sample_id' => function() {
            return factory(App\Sample::class)->create()->id;
        },
        'test_id' => function() {
            return rand(1, 10);
        },
        'processed_at' => $faker->dateTimeThisYear,
        'receiver_id' => function () {
            return factory(App\Person::class)->create()->id;
        },
        'deliverer_id' => function () {
            return factory(App\Person::class)->create()->id;
        },
        'collector_id' => function () {
            return factory(App\Person::class)->create()->id;
        }
    ];
});
