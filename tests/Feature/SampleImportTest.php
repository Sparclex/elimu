<?php

namespace Tests\Feature;

use Facades\Tests\Setup\SampleFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SampleImportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_import_samples()
    {
        $this->signInScientist();
        $file = $this->createTmpFile('imports/sample.xlsx');

        $this->postJson('/nova-vendor/sparclex/nova-import-card/endpoint/samples', [
            'file' => $file
        ])->assertSuccessful();

        $this->assertDatabaseHas('samples', [
            'sample_id' => 'XYZ123'
        ]);
    }

    /** @test */
    public function it_does_not_overwrite_existing_samples()
    {
        $this->signInScientist();

        $sample = SampleFactory::forManager($this->signInScientist())->create([
            'sample_id' => 'XYZ123'
        ]);
        $file = $this->createTmpFile('imports/sample.xlsx');

        $this->postJson('/nova-vendor/sparclex/nova-import-card/endpoint/samples', [
            'file' => $file
        ])->assertSuccessful();

        $this->assertDatabaseHas('samples', [
            'sample_id' => 'XYZ123',
            'subject_id' => $sample->subject_id
        ]);
    }

    /** @test */
    public function it_adds_a_mutation_to_an_existing_sample()
    {
        $this->signInScientist();

        $sample = SampleFactory::forManager($this->signInScientist())->create([
            'sample_id' => 'XYZ123'
        ]);

        $file = $this->createTmpFile('imports/sample.xlsx');

        $this->postJson('/nova-vendor/sparclex/nova-import-card/endpoint/samples', [
            'file' => $file
        ])->assertSuccessful();


        $this->assertDatabaseHas('sample_mutations', [
            'sample_id' => $sample->id,
            'sample_type_id' => DB::table('sample_types')
                ->where('name', 'Blood')
                ->pluck('id')
                ->first(),
            'quantity' => 2
        ]);
    }

    /** @test */
    public function it_stores_mutations()
    {
        $this->signInScientist();

        $sample = SampleFactory::forManager($this->signInScientist())->create([
            'sample_id' => 'XYZ123'
        ]);

        $file = $this->createTmpFile('imports/sample.xlsx');

        $this->postJson('/nova-vendor/sparclex/nova-import-card/endpoint/samples', [
            'file' => $file
        ])->assertSuccessful();

        $sampleTypeId = DB::table('sample_types')
            ->where('name', 'Blood')
            ->pluck('id')
            ->first();

        $this->assertDatabaseHas('storage', [
            'sample_id' => $sample->id,
            'sample_type_id' => $sampleTypeId,
            'position' => 1
        ]);
        $this->assertDatabaseHas('storage', [
            'sample_id' => $sample->id,
            'sample_type_id' => $sampleTypeId,
            'position' => 2
        ]);
    }
}