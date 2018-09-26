<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Experiment::class, function (Faker $faker) {
    return [
        'reagent_id' => function() { return factory(\App\Models\Reagent::class)->create()->id; },
        'study_id' => function() { return  factory(\App\Models\Study::class)->create()->id; }
    ];
});
