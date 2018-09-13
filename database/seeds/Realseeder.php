<?php

use App\Models\Experiment;
use App\Models\Sample;
use App\Models\SampleInformation;
use App\Models\Study;
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
        $assay = factory(\App\Models\Assay::class)->create();

        $this->call(UserSeeder::class);
        $study = factory(Study::class)->create();
        \App\Models\InputParameter::create(
            [
                'study_id' => $study->id,
                'assay_id' => $assay->id,
                'parameters' => [
                    [
                        'target' => 'PfvarATS',
                        'threshold' => 100,
                    ],
                    [
                        'target' => 'HsRNaseP',
                        'threshold' => 200,
                    ],
                    [
                        'target' => 'Pspp18S',
                        'threshold' => 200
                    ]
                ]
            ]);
        $sampleInformation4 = factory(SampleInformation::class)->create(['sample_id' => '5181967']);
        $sampleInformation1 = factory(SampleInformation::class)->create(['sample_id' => '7181969']);
        $sampleInformation2 = factory(SampleInformation::class)->create(['sample_id' => '6181968']);
        $sampleInformation3 = factory(SampleInformation::class)->create(['sample_id' => '8181970']);
        factory(Sample::class)->create(
            ['sample_information_id' => $sampleInformation1->id, 'study_id' => $study->id, 'quantity' => 0]);
        factory(Sample::class)->create(
            ['sample_information_id' => $sampleInformation2->id, 'study_id' => $study->id, 'quantity' => 0]);
        factory(Sample::class)->create(
            ['sample_information_id' => $sampleInformation3->id, 'study_id' => $study->id, 'quantity' => 0]);
        factory(Sample::class)->create(
            ['sample_information_id' => $sampleInformation4->id, 'study_id' => $study->id, 'quantity' => 0]);
        Experiment::create(
            [
                'assay_id' => 1,
                'requester_id' => 1,
                'requested_at' => Carbon::now(),
            ]);
    }
}
