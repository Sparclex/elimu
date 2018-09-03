<?php

use Faker\Generator as Faker;

$factory->define(\Sparclex\Lims\Models\Sample::class, function (Faker $faker) {
    return [
        'sample_type_id' => function() {
            return factory(\Sparclex\Lims\Models\SampleType::class)->create()->id;
        },
        'sample_information_id' => function() {
            return factory(\Sparclex\Lims\Models\SampleInformation::class)->create()->id;
        },
        'study_id' => function() {
            return factory(\Sparclex\Lims\Models\Study::class)->create()->id;
        },
        'quantity' => $faker->randomNumber(1)
    ];
});
