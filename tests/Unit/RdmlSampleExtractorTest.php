<?php

namespace Tests\Unit;

use App\Models\Data;
use App\Models\DataSample;
use App\Models\Sample;
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
        Data::unsetEventDispatcher();
    }

    /**
     * @test
     */
    public function it_should_extract_one_sample_from_the_rdml()
    {
        $data = factory(Data::class)->create();
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
        $extractor->handle($data->id);
        $this->assertEquals(
            2, DataSample::where(
            [
                'sample_id' => $sample->id,
                'data_id' => $data->id,
                'target' => 'PfvarATS',
            ])->count());
        $this->assertEquals(
            2, DataSample::where(
            [
                'sample_id' => $sample->id,
                'data_id' => $data->id,
                'target' => 'HsRNaseP',
            ])->count());
        $this->assertEquals(
            2, DataSample::where(
            [
                'sample_id' => $sample->id,
                'data_id' => $data->id,
                'target' => 'Pspp18S',
            ])->count());
    }

    /**
     * @test
     */
    public function it_should_extract_multiple_sample_from_the_rdml()
    {
        $data = factory(Data::class)->create();
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
        $extractor->handle($data->id);
        $this->assertDatabaseHas(
            'data_sample', [
            'sample_id' => $sample1->id,
            'data_id' => $data->id,
            'target' => 'PfvarATS',
        ]);
        $this->assertDatabaseHas(
            'data_sample', [
            'sample_id' => $sample2->id,
            'data_id' => $data->id,
            'target' => 'PfvarATS',
        ]);
        $this->assertDatabaseHas(
            'data_sample', [
            'sample_id' => $sample3->id,
            'data_id' => $data->id,
            'target' => 'PfvarATS',
        ]);
        $this->assertDatabaseHas(
            'data_sample', [
            'sample_id' => $sample4->id,
            'data_id' => $data->id,
            'target' => 'PfvarATS',
        ]);
    }

    /**
     * @test
     */
    public function it_should_inform_about_missing_samples()
    {
        $data = factory(Data::class)->create();

        $extractor = new Extractor(
            new Processor(
                file_get_contents(base_path('tests/resources/single-sample.xml')), [
                'PfvarATS' => 100,
                'HsRNaseP' => 200,
                'Pspp18S' => 200,
            ]));
        $this->assertFalse($extractor->handle($data->id));
    }
}
