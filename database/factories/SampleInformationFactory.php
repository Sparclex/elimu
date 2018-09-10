<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\SampleInformation::class, function (Faker $faker) {
    return [
        'subject_id' => $faker->md5,
        'sample_id' => $faker->unique->md5,
        'date' => $faker->dateTimeThisYear,
        'visit_id' => $faker->randomElement(['V1', 'V2', 'V2+14', 'CH+14']),
    ];
});
