<?php

use App\Models\AssayDefinitionFile;
use App\Models\SampleType;
use App\Models\Study;
use Faker\Generator as Faker;

$factory->define(AssayDefinitionFile::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'original_name' => $faker->word,
        'sample_type_id' => factory(SampleType::class),
        'path' => $faker->word,
        'result_type' => $faker->word,
        'study_id' => function() {
            return auth()->user()->study_id ?? factory(Study::class)->create()->id;
        },
        'parameters' => json_encode([]),
    ];
});
