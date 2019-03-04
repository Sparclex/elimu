<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\SampleMutation::class, function (Faker $faker) {
    return [
        'sample_id' => function() {
            return factory(\App\Models\Sample::class)->create()->id;
        },
        'sample_type_id' => function() {
            return factory(\App\Models\SampleType::class)->create()->id;
        },
        'quantity' => $faker->numberBetween(0, 10),
        'study_id' => function () {
            return \Illuminate\Support\Facades\Auth::user()->study_id ?? factory(\App\Models\Study::class)->create()->id;
        }
    ];
});
