<?php

use App\Models\AssayDefinitionFile;
use Faker\Generator as Faker;

$factory->define(App\Models\Assay::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->sentence(2),
        'assay_definition_file_id' => factory(AssayDefinitionFile::class),
        'instrument_id' => factory(\App\Models\Instrument::class),
        'protocol_id' => factory(\App\Models\Protocol::class),
        'primer_mix_id' => factory(\App\Models\PrimerMix::class)
    ];
});
