<?php

namespace Tests\Feature;

use App\Models\Sample;
use App\Models\SampleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_sample_aliquots_for_manual_actions()
    {
        $this->withoutExceptionHandling();

        $this->signInScientist();
        $sample = factory(Sample::class)->create();
        $sampleType = factory(SampleType::class)->create();

        // generate on creation
        $this->post("/nova-api/samples/{$sample->id}/attach/sample-types", [
            'sample-types' => $sampleType->id,
            'quantity' => 2,
            'viaRelationship' => 'sampleTypes'
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('storage', [
            'sample_type_id' => $sampleType->id,
            'sample_id' => $sample->id,
            'position' => 0,
        ]);
        $this->assertDatabaseHas('storage', [
            'sample_type_id' => $sampleType->id,
            'sample_id' => $sample->id,
            'position' => 1,
        ]);

        $sample2 = factory(Sample::class)->create();

        $this->post("/nova-api/samples/{$sample2->id}/attach/sample-types", [
            'sample-types' => $sampleType->id,
            'quantity' => 2,
            'viaRelationship' => 'sampleTypes'
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('storage', [
            'sample_type_id' => $sampleType->id,
            'sample_id' => $sample2->id,
            'position' => 2,
        ]);
        $this->assertDatabaseHas('storage', [
            'sample_type_id' => $sampleType->id,
            'sample_id' => $sample2->id,
            'position' => 3,
        ]);

        // modify on update

        $this->post("/nova-api/samples/{$sample2->id}/update-attached/sample-types/{$sampleType->id}", [
            'quantity' => 3,
            'viaRelationship' => 'sampleTypes',
            'sample-types' => $sampleType->id
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('storage', [
            'sample_type_id' => $sampleType->id,
            'sample_id' => $sample2->id,
            'position' => 4
        ]);

        $this->post("/nova-api/samples/{$sample2->id}/update-attached/sample-types/{$sampleType->id}", [
            'quantity' => 1,
            'viaRelationship' => 'sampleTypes',
            'sample-types' => $sampleType->id
        ])
            ->assertSuccessful();

        $this->assertDatabaseMissing('storage', [
            'sample_type_id' => $sampleType->id,
            'sample_id' => $sample2->id,
            'position' => 4
        ]);
        $this->assertDatabaseMissing('storage', [
            'sample_type_id' => $sampleType->id,
            'sample_id' => $sample2->id,
            'position' => 3
        ]);


        // remove on delete
        $this->novaDelete('/nova-api/sample-types/detach', $sampleType->id, [
            'viaResource' => 'samples',
            'viaResourceId' => $sample->id,
            'viaRelationship' => 'sampleTypes'
        ])->assertSuccessful();

        $this->assertDatabaseMissing('storage', [
            'sample_type_id' => $sampleType->id,
            'position' => 0,
        ]);

        $this->assertDatabaseMissing('storage', [
            'sample_type_id' => $sampleType->id,
            'position' => 1,
        ]);
    }

    /** @test */
    public function it_stores_sample_aliquots_for_importing()
    {
        $this->signInScientist();
        $file = $this->createTmpFile('imports/sample.xlsx');

        $this->postJson('/nova-vendor/sparclex/nova-import-card/endpoint/samples', [
            'file' => $file
        ])->assertSuccessful();

        $this->assertDatabaseHas('storage', [
            'sample_type_id' => SampleType::where('name', 'Blood')->first()->id,
            'sample_id' => Sample::where('sample_id', 'XYZ124')->first()->id,
            'position' => 0
        ]);
        $this->assertDatabaseHas('storage', [
            'sample_type_id' => SampleType::where('name', 'Blood')->first()->id,
            'sample_id' => Sample::where('sample_id', 'XYZ124')->first()->id,
            'position' => 3
        ]);
        $this->assertDatabaseHas('storage', [
            'sample_type_id' => SampleType::where('name', 'Blood')->first()->id,
            'sample_id' => Sample::where('sample_id', 'XYZ123')->first()->id,
            'position' => 4
        ]);
        $this->assertDatabaseHas('storage', [
            'sample_type_id' => SampleType::where('name', 'Blood')->first()->id,
            'sample_id' => Sample::where('sample_id', 'XYZ123')->first()->id,
            'position' => 5
        ]);
    }
}
