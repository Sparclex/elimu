<?php

use App\Models\SampleType;
use Faker\Generator as Faker;

$factory->define(\App\Models\Shipment::class, function (Faker $faker) {
    return [
        'study_id' => auth()->check() ? auth()->user()->study_id : factory(\App\Models\Study::class),
        'recipient' => $faker->name,
        'shipment_date' => $faker->date,
        'sample_type_id' => factory(SampleType::class)
    ];
});
