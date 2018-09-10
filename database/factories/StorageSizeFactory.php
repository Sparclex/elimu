<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\StorageSize::class, function (Faker $faker) {
    return [
        'study_id' => function () {
            return factory(\App\Models\Study::class)->create()->id;
        },
        'sample_type_id' => function()  {
            return factory(\App\Models\SampleType::class)->create()->id;
        },
        'size' => 5
    ];
});
