<?php

namespace Tests\Feature;

use App\Models\Assay;
use App\Models\Experiment;
use App\Models\Sample;
use App\Models\SampleInformation;
use App\Models\Study;
use App\Models\InputParameter;
use App\Policies\Authorization;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProcessRdmlTest extends TestCase
{
    use RefreshDatabase;

    private const SAMPLE_IDS = [
        '5181967',
        '6181968',
        '7181969',
        '8181970'
    ];

    private $study;
    private $samples;
    private $experiment;
    private $rdmls = [];


    protected function setUp()
    {
        parent::setup();
        $this->study = factory(Study::class)->create();
        $user = factory(User::class)->create(['role' => Authorization::SCIENTIST]);
        $user->studies()->attach($this->study);
        $user->study()->associate($this->study);
        $this->actingAs($user);
        $this->experiment = factory(Experiment::class)->create(
            [
                'study_id' => $this->study->id,
            ]
        );
        $inputParameters = InputParameter::create([
            'assay_id' => $this->experiment->reagent->assay_id,
            'study_id' => $this->study->id,
            'parameters' => $this->inputParameters()
        ]);
        $this->samples = $this->createSamples();

    }

    protected function tearDown()
    {
        parent::tearDown();

        foreach ($this->rdmls as $rdml) {
            if (file_exists($rdml)) {
                unlink($rdml);
            }
        }
    }


    /**
     * @test
     */
    public function it_should_process_a_valid_rdml()
    {
        $this->sendRdml('valid.rdml')
            ->dump();
    }

    /**
     * @test
     */
    public function it_should_decline_invalid_controls()
    {

    }

    /**
     * @test
     */
    public function it_should_decline_missing_thresholds()
    {

    }

    /**
     * @test
     */
    public function it_should_mark_as_repeat_samples_with_insufficient_amount_of_values()
    {

    }

    /**
     * @test
     */
    public function it_should_mark_as_repeat_samples_with_invalid_cutoff_stddev()
    {

    }

    /**
     * @test
     */
    public function it_should_store_results_with_average_cq_type_and_quantification()
    {

    }

    /**
     * @param $name
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function sendRdml($name)
    {
        $stub = base_path('tests/stubs/rdmls/' . $name);
        $stubName = str_random(8) . '.rdml';
        $path = sys_get_temp_dir() . '/' . $stubName;
        copy($stub, $path);

        $this->rdmls[] = $path;


        return $this->json('put', '/nova-api/experiments/' . $this->experiment->id, [
            'result_file' => new UploadedFile($path, $stubName, 'image/png', null, true),
            'file_type' => 'qPcr Rdml'
        ]);
    }

    /**
     * @param null $sampleIds
     * @return array
     */
    private function createSamples($sampleIds = null)
    {
        $sampleIds = $sampleIds ?? self::SAMPLE_IDS;
        $samples = [];
        foreach ($sampleIds as $sampleId) {
            $sampleInformation = factory(SampleInformation::class)->create([
                'sample_id' => $sampleId,
                'study_id' => $this->study->id
            ]);
            $samples[] = factory(Sample::class)->create(['sample_information_id' => $sampleInformation->id]);
        }
        return $samples;
    }

    /**
     * @return string
     */
    protected function inputParameters(): string
    {
        return "[
    {
        \"lod\": \"0.02\",
        \"ntc\": \"null\",
        \"calcq\": \"27.24\",
        \"fluor\": \"cy5\",
        \"quant\": \"yes\",
        \"slope\": \"-0.2909\",
        \"cutoff\": \"42\",
        \"target\": \"pspp18s\",
        \"calconc\": \"200\",
        \"negctrl\": \"null\",
        \"posctrl\": \"cqcal\u00b11\",
        \"qpcreff\": \"0.963\",
        \"pathogen\": \"Plasmodium spp\",
        \"intercept\": \"10.27\",
        \"minvalues\": \"2\",
        \"threshold\": \"100\",
        \"quant_type\": \"standard\",
        \"cuttoffstdev\": \"2\"
    },
    {
        \"lod\": \"0.1\",
        \"ntc\": \"null\",
        \"calcq\": \"25.13\",
        \"fluor\": \"fam\",
        \"quant\": \"yes\",
        \"slope\": \"-0.2916\",
        \"cutoff\": \"42\",
        \"target\": \"pfvarats\",
        \"calconc\": \"200\",
        \"negctrl\": \"null\",
        \"posctrl\": \"cqcal\u00b11\",
        \"qpcreff\": \"0.95\",
        \"pathogen\": \"P. falciparum\",
        \"intercept\": \"9.555\",
        \"minvalues\": \"2\",
        \"threshold\": \"200\",
        \"quant_type\": \"standard\",
        \"cuttoffstdev\": \"2\"
    },
    {
        \"lod\": \"\",
        \"ntc\": \"null\",
        \"calcq\": \"\",
        \"fluor\": \"vic\",
        \"quant\": \"no\",
        \"slope\": \"\",
        \"cutoff\": \"28\",
        \"target\": \"hsrnasep\",
        \"calconc\": \"\",
        \"negctrl\": \"<cutoff\",
        \"posctrl\": \"\",
        \"qpcreff\": \"\",
        \"pathogen\": \"Internal Control\",
        \"intercept\": \"\",
        \"minvalues\": \"2\",
        \"threshold\": \"100\",
        \"quant_type\": \"\",
        \"cuttoffstdev\": \"0.8\"
    }
]";
    }
}
