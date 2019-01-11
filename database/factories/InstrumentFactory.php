<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Instrument::class, function (Faker $faker) {
    return [
        'instrument_id' => $faker->unique()->md5,
        'name' => $faker->unique()->sentence,
        'serial_number' => $faker->unique()->md5,
        'responsible_id' => function() {
            return factory(\App\Models\Person::class)->create()->id;
        },
        'institution_id' => function() {
            return factory(\App\Models\Institution::class)->create()->id;
        },
        'laboratory_id' => function() {
            return factory(\App\Models\Laboratory::class)->create()->id;
        }
    ];
});
