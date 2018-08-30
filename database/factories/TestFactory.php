<?php

use Faker\Generator as Faker;

$factory->define(App\Test::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->randomElement([
            'Malaria',
            'Helminths',
            'PBME',
            'Humoral',
            'Serology',
            'Heratology',
            'Biochemistry',
            'Pregnancy',
            'GPCR',
            'Microbiology',
        ]),
    ];
});
