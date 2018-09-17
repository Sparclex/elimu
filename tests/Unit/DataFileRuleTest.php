<?php

namespace Tests\Unit;

use App\Models\Experiment;
use App\Models\Sample;
use App\Models\SampleInformation;
use App\Models\Study;
use App\Rules\DataFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\File;
use Tests\TestCase;

class DataFileRuleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Sample Ids given in the valid rdml / xml
     *
     * @var array
     */
    protected $sampleIds = [
        '5181967',
        '6181968',
        '7181969',
        '8181970',
    ];

    /**
     * @var \Illuminate\Contracts\Validation\Rule
     */
    protected $rule;
    protected $experiment;

    public function setUp()
    {
        parent::setUp();

        $this->experiment = factory(Experiment::class)->create();
        $this->rule = new DataFile($this->experiment->id);
    }

    /**
     * @test
     */
    public function it_should_accept_a_valid_rdml()
    {
        $samples = $this->createSamples();
        $this->experiment->samples()->saveMany($samples);

        $this->assertTrue($this->rule->passes('test', new File($this->resource('valid-rdml.rdml'))));
    }

    /**
     * @test
     */
    public function it_should_accept_a_valid_csv()
    {
        $samples = $this->createSamples();
        $this->experiment->samples()->saveMany($samples);

        $this->assertTrue($this->rule->passes('test', new File($this->resource('valid-csv.csv'))));
    }

    /**
     * @test
     */
    public function it_should_decline_a_rdml_with_too_many_samples()
    {
        $samples = $this->createSamples(array_slice($this->sampleIds, 0, 2));
        $this->experiment->samples()->saveMany($samples);
        $this->assertFalse($this->rule->passes('test', new File($this->resource('valid-rdml.rdml'))));
    }

    /**
     * @test
     */
    public function it_should_decline_a_rdml_with_not_enough_samples() {
        $samples = $this->createSamples(array_merge($this->sampleIds, ['anothersample']));
        $this->experiment->samples()->saveMany($samples);
        $this->assertFalse($this->rule->passes('test', new File($this->resource('valid-rdml.rdml'))));
    }

    /**
     * @test
     */
    public function it_should_decline_a_csv_with_too_many_samples()
    {
        $samples = $this->createSamples(array_slice($this->sampleIds, 0, 2));
        $this->experiment->samples()->saveMany($samples);
        $this->assertFalse($this->rule->passes('test', new File($this->resource('valid-csv.csv'))));


    }

    public function it_should_decline_a_csv_with_not_enough_samples() {
        $samples = $this->createSamples(array_merge($this->sampleIds, 'anothersample'));
        $this->experiment->samples()->saveMany($samples);
        $this->assertFalse($this->rule->passes('test', new File($this->resource('valid-csv.csv'))));
    }

    /**
     * @test
     */
    public function it_should_decline_an_invalid_rdml()
    {
        $this->assertFalse($this->rule->passes('test', new File($this->resource('without-controls-rdml.rdml'))));

        $this->assertFalse($this->rule->passes('test', new File($this->resource('without-samples-rdml.rdml'))));

        $this->assertFalse($this->rule->passes('test', new File($this->resource('invalid-rdml.rdml'))));
    }

    public function it_should_decline_an_invalid_csv()
    {
        $this->assertFalse($this->rule->passes('test', new File($this->resource('invalid-csv.rdml'))));
    }

    /**
     * @param array $sampleIds
     * @return array
     */
    private function createSamples($sampleIds = null)
    {
        $sampleIds = $sampleIds ?? $this->sampleIds;
        $samples = [];
        $study = factory(Study::class)->create();
        foreach($sampleIds as $sampleId) {
            $sampleInformation = factory(SampleInformation::class)->create(['sample_id' => $sampleId]);
            $samples[] = factory(Sample::class)->create(['sample_information_id' => $sampleInformation, 'study_id' => $study->id]);
        }
        return $samples;
    }
}
