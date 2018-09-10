<?php

namespace Tests\Feature;

use App\Models\SampleInformation;
use App\Models\SampleType;
use App\Models\Study;
use App\Models\Sample;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SampleBatchImporterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_should_import_a_single_sample()
    {
        $study = factory(Study::class)->create();
        $this->post('/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sample_type' => 'Sample type 1',
                    'quantity' => '0',
                    'sample_id' => 'abc',
                    'subject_id' => 'abc',
                    'collected_at' => Carbon::now()->format('Y-m-d'),
                    'visit_id' => 'abc'
                ]
            ]
        ]);
        $this->assertDatabaseHas('sample_informations', [
            'sample_id' => 'abc',
            'subject_id' => 'abc'
        ]);
        $this->assertDatabaseHas('sample_types', [
            'name' => 'Sample type 1',
        ]);
        $this->assertDatabaseHas('samples', [
            'sample_information_id' => 1,
            'sample_type_id' => 1,
            'study_id' => $study->id
        ]);
    }

    /**
     * @test
     */
    public function it_should_import_multiple_samples() {
        $study = factory(Study::class)->create();
        $this->post('/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sample_type' => 'Sample type 1',
                    'quantity' => '0',
                    'sample_id' => 'abc',
                    'subject_id' => 'abc',
                    'collected_at' => Carbon::now()->format('Y-m-d'),
                    'visit_id' => 'abc'
                ],
                [
                    'sample_type' => 'Sample type 1',
                    'quantity' => '0',
                    'sample_id' => 'bcd',
                    'subject_id' => 'abc',
                    'collected_at' => Carbon::now()->format('Y-m-d'),
                    'visit_id' => 'abc'
                ],
                [
                    'sample_type' => 'Sample type 1',
                    'quantity' => '0',
                    'sample_id' => 'cde',
                    'subject_id' => 'abc',
                    'collected_at' => Carbon::now()->format('Y-m-d'),
                    'visit_id' => 'abc'
                ],
                [
                    'sample_type' => 'Sample type 1',
                    'quantity' => '0',
                    'sample_id' => 'def',
                    'subject_id' => 'abc',
                    'collected_at' => Carbon::now()->format('Y-m-d'),
                    'visit_id' => 'abc'
                ]
            ]
        ]);
        $this->assertEquals(4, SampleInformation::count());
        $this->assertEquals(1, SampleType::count());
        $this->assertEquals(4, Sample::count());

        $this->post('/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sample_type' => 'Sample type 2',
                    'quantity' => '0',
                    'sample_id' => 'abc',
                    'subject_id' => 'abc',
                    'collected_at' => Carbon::now()->format('Y-m-d'),
                    'visit_id' => 'abc'
                ]
            ]
        ]);
        $this->assertEquals(4, SampleInformation::count());
        $this->assertEquals(2, SampleType::count());
        $this->assertEquals(5, Sample::count());
    }

    /**
     * @test
     */
    public function it_should_ignore_already_existing_data() {
        $sample = factory(Sample::class)->create(['quantity' => '0']);
        $this->post('/nova-vendor/lims/import-samples', [
            'study' => $sample->study->id,
            'samples' => [
                [
                    'sample_type' => $sample->sample_type_id,
                    'quantity' => '0',
                    'sample_id' => $sample->sampleInformation->sample_id,
                    'subject_id' => $sample->sampleInformation->subject_id,
                    'collected_at' => $sample->sampleInformation->collected_at,
                    'visit_id' => $sample->sampleInformation->visit_id
                ]
            ]
        ]);
        $this->assertEquals(1, SampleInformation::count());
        $this->assertEquals(1, SampleType::count());
        $this->assertEquals(1, Sample::count());
    }

    /**
     * @test
     */
    public function it_should_only_create_new_sample_types() {
        $study = factory(Study::class)->create();
        $sampleType = factory(SampleType::class)->create();
        $this->post('/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sample_type' => $sampleType->name,
                    'quantity' => '0',
                    'sample_id' => 'abc',
                    'subject_id' => 'abc',
                    'collected_at' => Carbon::now()->format('Y-m-d'),
                    'visit_id' => 'abc'
                ]
            ]
        ]);
        $this->assertEquals(1, SampleInformation::count());
        $this->assertEquals(1, SampleType::count());
        $this->assertEquals(1, Sample::count());

        $this->post('/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sample_type' => $sampleType->name . "-new",
                    'quantity' => '0',
                    'sample_id' => 'abc',
                    'subject_id' => 'abc',
                    'collected_at' => Carbon::now()->format('Y-m-d'),
                    'visit_id' => 'abc'
                ]
            ]
        ]);

        $this->assertEquals(1, SampleInformation::count());
        $this->assertEquals(2, SampleType::count());
        $this->assertEquals(2, Sample::count());

    }
}
