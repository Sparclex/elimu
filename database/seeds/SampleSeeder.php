<?php

use App\Models\Experiment;
use App\Models\Sample;
use App\Models\Study;
use Illuminate\Database\Seeder;

class SampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $samples = factory(Sample::class, 10)->create([
            'quantity' => 0,
            'study_id' => factory(Study::class)->create(['name' => 'Example Study'])->id
        ]);
        $experiment = Experiment::create([
            'assay_id' => 1,
            'requester_id' => 1,
            'requested_at' => \Illuminate\Support\Carbon::now()
        ]);
        $experiment->samples()->saveMany($samples);
    }
}
