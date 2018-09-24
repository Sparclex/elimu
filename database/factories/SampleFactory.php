<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Sample::class, function (Faker $faker) {
    return [
        'sample_type_id' => function() {
            return factory(\App\Models\SampleType::class)->create()->id;
        },
        'sample_information_id' => function() {
            return factory(\App\Models\SampleInformation::class)->create()->id;
        },
        'quantity' => $faker->randomNumber(1)
    ];
});
