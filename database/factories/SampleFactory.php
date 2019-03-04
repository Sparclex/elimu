<?php

use App\Models\Sample;
use App\Models\SampleMutation;
use App\Models\SampleType;
use Faker\Generator as Faker;

$factory->define(Sample::class, function (Faker $faker) {
    return [
        'subject_id' => $faker->md5,
        'sample_id' => $faker->unique->md5,
        'collected_at' => $faker->dateTimeThisYear,
        'visit_id' => $faker->randomElement(['V1', 'V2', 'V2+14', 'CH+14']),
        'study_id' => function () {
            return \Illuminate\Support\Facades\Auth::user()->study_id ?? factory(\App\Models\Study::class)->create()->id;
        }
    ];
});