<?php

use App\Models\Experiment;
use App\Models\Reagent;
use App\Models\Sample;
use App\Models\SampleInformation;
use App\Models\Storage;
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
        $reagent = factory(Reagent::class)->create();
        $assay = $reagent->assay;
        $this->call(UserSeeder::class);
        $study = factory(Study::class)->create();
        $sampleType = factory(\App\Models\SampleType::class)->create();
        $study->sampleTypes()->attach($sampleType->id, ['size' => 3]);
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
        $sampleInformation4 = factory(SampleInformation::class)->create([
            'study_id' => $study->id,
            'sample_id' => '5181967'
        ]);
        $sampleInformation1 = factory(SampleInformation::class)->create([
            'study_id' => $study->id,
            'sample_id' => '7181969'
        ]);
        $sampleInformation2 = factory(SampleInformation::class)->create([
            'study_id' => $study->id,
            'sample_id' => '6181968'
        ]);
        $sampleInformation3 = factory(SampleInformation::class)->create([
            'study_id' => $study->id,
            'sample_id' => '8181970'
        ]);
        $experiment = Experiment::create(
            [
                'study_id' => $study->id,
                'requester_id' => 1,
                'reagent_id' => $reagent->id,
                'requested_at' => Carbon::now(),
            ]);
        $experiment->samples()->saveMany([
            factory(Sample::class)->create([
                'sample_information_id' => $sampleInformation1->id,
                'quantity' => 2,
                'sample_type_id' => 1
            ]),
            factory(Sample::class)->create([
                'sample_information_id' => $sampleInformation2->id,
                'quantity' => 2,
                'sample_type_id' => 1
            ]),
            factory(Sample::class)->create([
                'sample_information_id' => $sampleInformation3->id,
                'quantity' => 2,
                'sample_type_id' => 1
            ]),
            factory(Sample::class)->create([
                'sample_information_id' => $sampleInformation4->id,
                'quantity' => 2,
                'sample_type_id' => 1
            ])
        ]);
        Storage::generateStoragePosition(1, $study->id, $sampleType->id, 2);
        Storage::generateStoragePosition(2, $study->id, $sampleType->id, 2);
        Storage::generateStoragePosition(3, $study->id, $sampleType->id, 2);
        Storage::generateStoragePosition(4, $study->id, $sampleType->id, 2);

    }
}
