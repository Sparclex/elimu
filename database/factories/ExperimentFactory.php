<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Experiment::class, function (Faker $faker) {
    return [
        'study_id' => function () {
            return \Illuminate\Support\Facades\Auth::user()->study_id ?? factory(\App\Models\Study::class)->create()->id;
        },
        'assay_id' => function () {
            return factory(\App\Models\Assay::class)->create()->id;
        },
        'requested_at' => $faker->dateTime,
        'comment' => $faker->paragraph
    ];
});
