<?php

use App\Models\AssayDefinitionFile;
use App\Models\Person;
use App\Models\Reagent;
use Faker\Generator as Faker;

$factory->define(App\Models\Assay::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->sentence(2),
        'assay_definition_file_id' => factory(AssayDefinitionFile::class),
        'instrument_id' => factory(\App\Models\Instrument::class),
        'protocol_id' => factory(\App\Models\Protocol::class),
        'reagent_id' => factory(Reagent::class),
        'creator_id' => factory(Person::class),

    ];
});
