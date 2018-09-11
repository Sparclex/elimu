<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Data::class, function (Faker $faker) {
    return [
        'experiment_id' => function() { return factory(\App\Models\Experiment::class)->create()->id; },
        'type' => 'Rdml',
        'file' => 'somefile.rdml'
    ];
});
