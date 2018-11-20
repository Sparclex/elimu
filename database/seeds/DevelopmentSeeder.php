<?php


use App\Models\Assay;
use App\Models\Reagent;
use App\Models\SampleType;
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
        $sampleIdsInRdml = [[
            "4179473",
            "3179472",
            "7179485",
            "5179474",
            "1179470",
            "9179487",
            "8179486",
            "9179469",
            "2179471",
            ],[
            "1179587",
            "2179588",
            "7179557",
            "6179583",
            "7179584",
            "8179558",
            "8179585",
            "9179559",
            ],[
            "6179592",
            "7179593",
            "8179594",
            "9179595",
            "1179596",
            "5176792",
            "5176801",
            "9176805",
            "1179588",
            ],[
            "2179597",
            "3179598",
            "9178623",
            "3178626",
            "4178627",
            "7176749",
            "8176750",
            "9176751",
            "1178624",
            ],[
            "1178624",
            ],[
            "1176824",
            "2176825",
            "3176826",
            "4176827",
            "5176828",
            "6176829",
            "7176830",
            "8176831",
            "9176832",
            "1176833",
            "2176834",
            "3176835",
            "9176778",
            "1176779",
            "2176780",
            "3176781",
            ],[
            "8179450",
            "6179448",
            "5179447",
            "4179419",
            "3179418",
            "5179429",
            "7179431",
            "1179416",
            "9176823",
            "8176822",
            "1176752",
            "5179591",
            "2176753",
            "6176820",
            "7176821",
            "4179590",
            "6176819",
            "1179605",
            "3172607",
            "2179606",
            "4179608",
            "5179609",
            "6179610",
            "7179611",
            ],[
            "7179413",
            "8179414",
            "9179415",
            "3179445",
            "4179446",
            "7179449",
            "9179451",
            "1179452",
            "6176820",
            "3179625",
            "4179626",
            "5179627",
            "6179628",
            "7179629",
            "8179630",
            "9179631",
            "1179632",
            "7176821",
            "9179613",
            "1179614",
            "2179615",
            "3179616",
            "4179617",
            "5179618",
            "6179619",
            "7179620",
            "4179608",
        ]
        ];
        foreach($sampleIdsInRdml as $experimentNumber => $experimentSamples) {
            factory(SampleType::class)->create();
             $samples = factory(\App\Models\Sample::class, count($experimentSamples))
            ->make([
                'sample_type_id' => 1
            ])
            ->each(function ($sample, $key) use ($experimentSamples, $study_id) {
                $sample->sample_information_id = factory(\App\Models\SampleInformation::class)
                    ->create([
                        'study_id' => $study_id,
                        'sample_id' => $experimentSamples[$key]
                    ])->id;
            });
            $experiment = factory(\App\Models\Experiment::class)
                ->create([
                    'study_id' => $study_id,
                    'reagent_id' => factory(Reagent::class)
                        ->create([
                            'name' => 'Experiment ' . ($experimentNumber + 1),
                            'assay_id' => factory(Assay::class)->create([
                                'name' => 'Experiment ' . ($experimentNumber + 1)
                            ])->id
                        ])->id
                ]);
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
                        "posctrl" => "30",
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
                        "posctrl" => "30",
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
                        "negctrl" => "cutoff",
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
}
