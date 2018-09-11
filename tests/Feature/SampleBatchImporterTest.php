<?php

namespace Tests\Feature;

use App\Models\Sample;
use App\Models\SampleInformation;
use App\Models\SampleType;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\NovaTest;

class SampleBatchImporterTest extends NovaTest
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_should_import_a_single_sample()
    {
        $study = factory(Study::class)->create();
        $this->json(
            'POST', '/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sampleType' => 'Sample type 1',
                    'quantity' => '0',
                    'sampleId' => 'abc',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
            ],
        ])->assertStatus(200);
        $this->assertDatabaseHas(
            'sample_informations', [
            'sample_id' => 'abc',
            'subject_id' => 'abc',
        ]);
        $this->assertDatabaseHas(
            'sample_types', [
            'name' => 'Sample type 1',
        ]);
        $this->assertDatabaseHas(
            'samples', [
            'sample_information_id' => SampleInformation::where('sample_id', 'abc')->first()->id,
            'sample_type_id' => SampleType::where('name', 'Sample type 1')->first()->id,
            'study_id' => $study->id,
        ]);
    }

    /**
     * @test
     */
    public function it_should_import_multiple_samples()
    {
        $study = factory(Study::class)->create();
        $this->json(
            'POST', '/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sampleType' => 'Sample type 1',
                    'quantity' => '0',
                    'sampleId' => 'abc',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
                [
                    'sampleType' => 'Sample type 1',
                    'quantity' => '0',
                    'sampleId' => 'bcd',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
                [
                    'sampleType' => 'Sample type 1',
                    'quantity' => '0',
                    'sampleId' => 'cde',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
                [
                    'sampleType' => 'Sample type 1',
                    'quantity' => '0',
                    'sampleId' => 'def',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
            ],
        ])->assertStatus(200);
        $this->assertEquals(4, SampleInformation::count());
        $this->assertEquals(1, SampleType::count());
        $this->assertEquals(4, Sample::count());

        $this->json(
            'POST', '/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sampleType' => 'Sample type 2',
                    'quantity' => '0',
                    'sampleId' => 'abc',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
            ],
        ])->assertStatus(200);
        $this->assertEquals(4, SampleInformation::count());
        $this->assertEquals(2, SampleType::count());
        $this->assertEquals(5, Sample::count());
    }

    /**
     * @test
     */
    public function it_should_ignore_already_existing_data()
    {
        $sample = factory(Sample::class)->create(['quantity' => '0']);
        $this->json(
            'POST', '/nova-vendor/lims/import-samples', [
            'study' => $sample->study->id,
            'samples' => [
                [
                    'sampleType' => $sample->sampleType->name,
                    'quantity' => '0',
                    'sampleId' => $sample->sampleInformation->sample_id,
                    'subjectId' => $sample->sampleInformation->subject_id,
                    'collectionDate' => $sample->sampleInformation->collected_at->format('Y-m-d H:i'),
                    'visitId' => $sample->sampleInformation->visit_id,
                ],
            ],
        ])->assertStatus(200);
        $this->assertEquals(1, SampleInformation::count());
        $this->assertEquals(1, SampleType::count());
        $this->assertEquals(1, Sample::count());
    }

    /**
     * @test
     */
    public function it_should_only_create_new_sample_types()
    {
        $study = factory(Study::class)->create();
        $sampleType = factory(SampleType::class)->create();
        $this->json(
            'POST', '/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sampleType' => $sampleType->name,
                    'quantity' => '0',
                    'sampleId' => 'abc',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
            ],
        ])->assertStatus(200);
        $this->assertEquals(1, SampleInformation::count());
        $this->assertEquals(1, SampleType::count());
        $this->assertEquals(1, Sample::count());

        $this->json(
            'POST', '/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sampleType' => $sampleType->name."-new",
                    'quantity' => '0',
                    'sampleId' => 'abc',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
            ],
        ])->assertStatus(200);

        $this->assertEquals(1, SampleInformation::count());
        $this->assertEquals(2, SampleType::count());
        $this->assertEquals(2, Sample::count());
    }

    /**
     * @test
     */
    public function it_should_store_an_imported_sample()
    {
        $study = factory(Study::class)->create();
        $sampleType = factory(SampleType::class)->create();
        $study->sampleTypes()->attach($sampleType->id, ['size' => 2]);
        $this->json(
            'POST', '/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sampleType' => $sampleType->name,
                    'quantity' => '1',
                    'sampleId' => 'abc',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
            ],
        ])->assertStatus(200);
        $this->assertDatabaseHas(
            'storage', [
            'sample_id' => Sample::latest()->first()->id,
            'box' => 1,
            'position' => 1,
        ]);
        $this->json(
            'POST', '/nova-vendor/lims/import-samples', [
            'study' => $study->id,
            'samples' => [
                [
                    'sampleType' => $sampleType->name,
                    'quantity' => '2',
                    'sampleId' => 'cde',
                    'subjectId' => 'abc',
                    'collectionDate' => Carbon::now()->format('Y-m-d H:i'),
                    'visitId' => 'abc',
                ],
            ],
        ])->assertStatus(200);
        $this->assertDatabaseHas(
            'storage', [
            'sample_id' => Sample::where('quantity', 2)->first()->id,
            'box' => 2,
            'position' => 1,
        ]);
    }
}
