<?php

use App\Models\Assay;
use App\Models\Result;
use App\Models\Sample;
use Faker\Generator as Faker;

$factory->define(Result::class, function (Faker $faker) {
    return [
        'sample_id' => function () {
            return factory(Sample::class)->create()->id;
        },
        'assay_id' => function () {
            return factory(Assay::class)->create()->id;
        },
        'target' => $faker->word
    ];
});
