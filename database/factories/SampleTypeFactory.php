<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\SampleType::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->randomElement([
            'Whole blood',
            'Serum',
            'Stool',
            'Urine',
            'PBMC',
            'Plasma',
            'Pax',
            'Pregnancy',
            'GPCR',
            'Microbiology',
        ]),
    ];
});
