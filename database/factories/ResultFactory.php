<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Result::class, function (Faker $faker) {
    return [
        'sample_id' => function () {
            return factory(\App\Models\Sample::class)->create()->id;
        },
        'experiment_id' => function () {
            return factory(\App\Models\Experiment::class)->create()->id;
        },
        'target' => $faker->word,
        'value' => $faker->numberBetween(0, 50),
    ];
});

$factory->afterCreating(\App\Models\Result::class, function ($result, $faker) {
    factory(\App\Models\ResultData::class, 10)->create([
        'result_id' => $result->id
    ]);
    // First or create. Make sure the result is also in the requested experiment list
    if (!\Illuminate\Support\Facades\DB::table('requested_experiments')
        ->where('sample_id', $result->sample_id)
        ->where('experiment_id', $result->experiment_id)
        ->first()) {
        \Illuminate\Support\Facades\DB::table('requested_experiments')
            ->insert([
                'sample_id' => $result->sample_id,
                'experiment_id' => $result->experiment_id
            ]);
    }
});
