<?php

use Faker\Generator as Faker;

$factory->define(\Sparclex\Lims\Models\StorageSize::class, function (Faker $faker) {
    return [
        'study_id' => function () {
            return factory(\Sparclex\Lims\Models\Study::class)->create()->id;
        },
        'sample_type_id' => function()  {
            return factory(\Sparclex\Lims\Models\SampleType::class)->create()->id;
        },
        'size' => 5
    ];
});
