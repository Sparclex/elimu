<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Protocol::class, function (Faker $faker) {
    return [
        'protocol_id' => $faker->unique()->md5,
        'name' => $faker->word,
        'version' => $faker->numberBetween(1, 5),
        'implemented_at' => $faker->date,
        'attachment_name' => 'no attachment',
        'attachment_path' => 'no attachment',
        'responsible_id' => function() {
            return factory(\App\Models\Person::class)->create()->id;
        },
        'institution_id' => function() {
            return factory(\App\Models\Institution::class)->create()->id;
        },
    ];
});
