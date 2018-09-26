<?php

namespace Tests\Unit;


use App\Models\Experiment;
use App\Models\Sample;
use App\Models\SampleData;
use App\Models\SampleInformation;
use App\ResultHandlers\Rdml\Extractor;
use App\ResultHandlers\Rdml\Processor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RdmlSampleExtractorTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        Sample::unsetEventDispatcher();
    }

    /**
     * @test
     */
    public function it_should_extract_one_sample_from_the_rdml()
    {
        $experiment = factory(Experiment::class)->create();
        $sample = factory(Sample::class)->create(
            [
                'sample_information_id' => factory(SampleInformation::class)->create(['sample_id' => '9181971'])->id,
            ]);
        $extractor = new Extractor(
            new Processor(
                file_get_contents(base_path('tests/resources/single-sample.xml')), [
                'PfvarATS' => 100,
                'HsRNaseP' => 200,
                'Pspp18S' => 200,
            ]));
        $extractor->handle($experiment->id);
        $this->assertEquals(
            2, SampleData::where(
            [
                'sample_id' => $sample->id,
                'experiment_id' => $experiment->id,
                'target' => 'PfvarATS',
            ])->count());
        $this->assertEquals(
            2, SampleData::where(
            [
                'sample_id' => $sample->id,
                'experiment_id' => $experiment->id,
                'target' => 'HsRNaseP',
            ])->count());
        $this->assertEquals(
            2, SampleData::where(
            [
                'sample_id' => $sample->id,
                'experiment_id' => $experiment->id,
                'target' => 'Pspp18S',
            ])->count());
    }

    /**
     * @test
     */
    public function it_should_extract_multiple_sample_from_the_rdml()
    {
        $experiment = factory(Experiment::class)->create();
        $sample1 = factory(Sample::class)->create(
            [
                'sample_information_id' => factory(SampleInformation::class)->create(['sample_id' => '6181968'])->id,
            ]);
        $sample2 = factory(Sample::class)->create(
            [
                'sample_information_id' => factory(SampleInformation::class)->create(['sample_id' => '5181967'])->id,
            ]);
        $sample3 = factory(Sample::class)->create(
            [
                'sample_information_id' => factory(SampleInformation::class)->create(['sample_id' => '7181969'])->id,
            ]);
        $sample4 = factory(Sample::class)->create(
            [
                'sample_information_id' => factory(SampleInformation::class)->create(['sample_id' => '8181970'])->id,
            ]);
        $extractor = new Extractor(
            new Processor(
                file_get_contents(base_path('tests/resources/multiple-samples.xml')), [
                'PfvarATS' => 100,
                'HsRNaseP' => 200,
                'Pspp18S' => 200,
            ]));
        $extractor->handle($experiment->id);
        $this->assertDatabaseHas(
            'sample_data', [
            'sample_id' => $sample1->id,
            'experiment_id' => $experiment->id,
            'target' => 'PfvarATS',
        ]);
        $this->assertDatabaseHas(
            'sample_data', [
            'sample_id' => $sample2->id,
            'experiment_id' => $experiment->id,
            'target' => 'PfvarATS',
        ]);
        $this->assertDatabaseHas(
            'sample_data', [
            'sample_id' => $sample3->id,
            'experiment_id' => $experiment->id,
            'target' => 'PfvarATS',
        ]);
        $this->assertDatabaseHas(
            'sample_data', [
            'sample_id' => $sample4->id,
            'experiment_id' => $experiment->id,
            'target' => 'PfvarATS',
        ]);
    }

    /**
     * @test
     */
    public function it_should_inform_about_missing_samples()
    {
        $experiment = factory(Experiment::class)->create();

        $extractor = new Extractor(
            new Processor(
                file_get_contents(base_path('tests/resources/single-sample.xml')), [
                'PfvarATS' => 100,
                'HsRNaseP' => 200,
                'Pspp18S' => 200,
            ]));
        $this->assertFalse($extractor->handle($experiment->id));
    }
}
