<?php

use App\Models\Experiment;
use App\Models\Sample;
use App\Models\Study;
use App\Models\SampleInformation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class Realseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AssaySeeder::class);
        $this->call(UserSeeder::class);
        $study = factory(Study::class)->create();
        $sampleInformation = factory(SampleInformation::class)->create(['sample_id' => '918197']);
        $sample = factory(Sample::class)->create(
            ['sample_information_id' => $sampleInformation->id, 'study_id' => $study->id, 'quantity' => 0]);
        Experiment::create(
            [
                'assay_id' => 1,
                'requester_id' => 1,
                'requested_at' => Carbon::now(),
            ]);

    }
}
