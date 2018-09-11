<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Experiment::class, function (Faker $faker) {
    return [
        'assay_id' => function() { return factory(\App\Models\Assay::class)->create()->id; }
    ];
});
