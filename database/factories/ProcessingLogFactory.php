<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ProcessingLog::class, function (Faker $faker) {
    return [
        'processed_at' => $faker->date,
        'receiver_id' => function() {
            return factory(\App\Models\Person::class)->create()->id;
        },
        'deliverer_id' => function() {
            return factory(\App\Models\Person::class)->create()->id;
        },
        'collector_id' => function() {
            return factory(\App\Models\Person::class)->create()->id;
        },
        'comment' => $faker->text
    ];
});
