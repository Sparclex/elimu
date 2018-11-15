<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\ResultData::class, function (Faker $faker) {
    return [
        'result_id' => function() {
            return factory(\App\Models\Result::class)->create()->id;
        },
        'primary_value' => $faker->randomNumber(2),
        'secondary_value' => $faker->randomNumber(2),
    ];
});
