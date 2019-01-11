<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Assay::class, function (Faker $faker) {
    $resultTypes = array_keys(config('lims.result_types'));

    return [
        'name' => $faker->unique()->sentence(2),
        'result_type' => $faker->randomElement($resultTypes),
        'instrument_id' => function() {
            return factory(\App\Models\Instrument::class)->create()->id;
        },
        'protocol_id' => function() {
            return factory(\App\Models\Protocol::class)->create()->id;
        },
        'primer_mix_id' => function() {
            return factory(\App\Models\PrimerMix::class)->create()->id;
        }
    ];
});
