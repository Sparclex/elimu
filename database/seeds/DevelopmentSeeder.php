<?php


use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $study_id = factory(\App\Models\Study::class)->create()->id;
        $user = factory(App\User::class)->create([
            'email' => 'silvan.wehner@gmail.com',
            'password' => \Hash::make('12345'),
            'name' => 'Silvan Wehner',
            'study_id' => $study_id,
            'role' => 'Administrator'
        ]);
        factory(App\User::class)->create([
            'email' => 'schindler.tobi@gmail.com',
            'name' => 'Tobias Schindler',
            'password' => \Illuminate\Support\Facades\Hash::make('ihibagamoyo'),
            'timezone' => 'Europe/Zurich',
            'role' => 'Administrator'
        ]);
        $user->studies()->attach($study_id);
        /*
                factory(\App\Models\Result::class, 10)->create([
                    'experiment_id' => factory(\App\Models\Experiment::class)
                        ->create(compact('study_id'))->id,
                    'sample_id' => function () use($study_id) {
                        return factory(\App\Models\Sample::class)
                            ->create([
                                'sample_information_id' => function () use($study_id) {
                                    return factory(\App\Models\SampleInformation::class)
                                        ->create(compact('study_id'))->id;
                                }])->id;
                    }
                ]);
        */
        $sampleIdsInRdml = [
            "4179473",
            "3179472",
            "7179485",
            "5179474",
            "1179470",
            "9179487",
            "8179486",
            "9179469",
            "2179471"
        ];
        $samples = factory(\App\Models\Sample::class, count($sampleIdsInRdml))
            ->make()
            ->each(function ($sample, $key) use ($sampleIdsInRdml, $study_id) {
                $sample->sample_information_id = factory(\App\Models\SampleInformation::class)
                    ->create([
                        'study_id' => $study_id,
                        'sample_id' => $sampleIdsInRdml[$key]
                    ])->id;
            });
        $experiment = factory(\App\Models\Experiment::class)
            ->create(compact('study_id'));
        $experiment->samples()
            ->saveMany($samples);

        \App\Models\InputParameter::create([
            'study_id' => $study_id,
            'assay_id' => $experiment->reagent->assay->id,
            'parameters' =>
                [
                    [
                        "lod" => "0.02",
                        "ntc" => "null",
                        "calcq" => "27.24",
                        "fluor" => "cy5",
                        "quant" => "yes",
                        "slope" => "-0.2909",
                        "cutoff" => "42",
                        "target" => "Pspp18S",
                        "calconc" => "200",
                        "negctrl" => "null",
                        "posctrl" => "cqcal\u00b11",
                        "qpcreff" => "0.963",
                        "pathogen" => "Plasmodium spp",
                        "intercept" => "10.27",
                        "minvalues" => "2",
                        "threshold" => "100",
                        "quant_type" => "standard",
                        "cuttoffstdev" => "2"
                    ],
                    [
                        "lod" => "0.1",
                        "ntc" => "null",
                        "calcq" => "25.13",
                        "fluor" => "fam",
                        "quant" => "yes",
                        "slope" => "-0.2916",
                        "cutoff" => "42",
                        "target" => "PfvarATS",
                        "calconc" => "200",
                        "negctrl" => "null",
                        "posctrl" => "cqcal\u00b11",
                        "qpcreff" => "0.95",
                        "pathogen" => "P. falciparum",
                        "intercept" => "9.555",
                        "minvalues" => "2",
                        "threshold" => "200",
                        "quant_type" => "standard",
                        "cuttoffstdev" => "2"
                    ],
                    [
                        "lod" => "",
                        "ntc" => "null",
                        "calcq" => "",
                        "fluor" => "vic",
                        "quant" => "no",
                        "slope" => "",
                        "cutoff" => "28",
                        "target" => "HsRNaseP",
                        "calconc" => "",
                        "negctrl" => "<cutoff",
                        "posctrl" => "",
                        "qpcreff" => "",
                        "pathogen" => "Internal Control",
                        "intercept" => "",
                        "minvalues" => "2",
                        "threshold" => "100",
                        "quant_type" => "",
                        "cuttoffstdev" => "0.8"
                    ]
                ]
        ]);

    }
}
