<?php

namespace Tests\Feature;

use App\Models\Sample;
use App\Models\SampleType;
use Facades\Tests\Setup\SampleFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SamplesTest extends TestCase
{
    use RefreshDatabase;

    private const RESOURCE_URI = '/nova-api/samples';

    /** @test */
    public function a_scientist_can_view_samples()
    {
        $sample = SampleFactory::forManager($this->signIn())->create();

        $this->get(self::RESOURCE_URI)->assertSuccessful();

        $this->get(self::RESOURCE_URI . "/{$sample->id}")
            ->assertSuccessful();

    }

    /** @test */
    public function a_scientist_can_create_samples()
    {
        $this->withoutExceptionHandling();
        $this->signInScientist();
        $sample = factory(Sample::class)->raw();

        $this->post(self::RESOURCE_URI, $sample)
            ->assertSuccessful();
    }

    /** @test */
    public function a_scientist_can_update_samples()
    {
        $sample = SampleFactory::forManager($this->signIn())->create();

        $this->put(self::RESOURCE_URI . "/{$sample->id}", $sample->toArray())
            ->assertSuccessful();
    }


    /** @test */
    public function a_scientist_can_delete_samples()
    {
        $sample = SampleFactory::forManager($this->signIn())->create();

        $this->novaDelete(self::RESOURCE_URI, $sample->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('samples', $sample->toArray());
    }

    /** @test */
    public function a_monitor_can_only_view_a_sample()
    {
        $user = $this->signInMonitor();

        $this->get(self::RESOURCE_URI)->assertSuccessful();

        $sample = factory(Sample::class)->raw(['study_id' => $user->study->id]);

        $this->post(self::RESOURCE_URI, $sample)
            ->assertForbidden();

        $sample = factory(Sample::class)->create(['study_id' => $user->study->id]);

        $this->get(self::RESOURCE_URI . "/{$sample->id}")
            ->assertSuccessful();

        $this->put(self::RESOURCE_URI . "/{$sample->id}", $sample->toArray())
            ->assertForbidden();

        $this->novaDelete(self::RESOURCE_URI, $sample->id)
            ->assertSuccessful();

        $this->assertDatabaseHas('samples', $sample->toArray());
    }

    /** @test */
    public function a_sample_has_a_type_without_storing()
    {
        $this->withoutExceptionHandling();
        $this->signInScientist();
        $sample = factory(Sample::class)->raw();
        $sampleType = factory(SampleType::class)->create();
        $aliquots = [
            'sampleTypes' => json_encode([
                [
                    'id' => $sampleType->id,
                    'quantity' => ''
                ]
            ])
        ];
        $this->post(self::RESOURCE_URI, array_merge($sample, $aliquots))
            ->assertSuccessful();

        $this->assertDatabaseHas('sample_mutations', [
            'sample_type_id' => $sampleType->id,
            'quantity' => null,
        ]);
    }

    /** @test */
    public function a_sample_can_store_a_type()
    {
        $this->withoutExceptionHandling();
        $user = $this->signInScientist();
        $sample = factory(Sample::class)->raw();
        $sampleType = factory(SampleType::class)->create();
        $user->study->sampleTypes()->attach($sampleType, ['rows' => 10, 'columns' => 10]);
        $aliquots = [
            'sampleTypes' => json_encode([
                [
                    'id' => $sampleType->id,
                    'quantity' => 5
                ]
            ])
        ];

        $this->post(self::RESOURCE_URI, array_merge($sample, $aliquots))
            ->assertSuccessful();

        $this->assertDatabaseHas('storage', [
            'study_id' => $user->study_id,
            'sample_type_id' => $sampleType->id,
            'position' => 0,
        ]);

        $this->assertDatabaseHas('storage', [
            'study_id' => $user->study_id,
            'sample_type_id' => $sampleType->id,
            'position' => 4,
        ]);

        $this->assertDatabaseMissing('storage', [
            'study_id' => $user->study_id,
            'sample_type_id' => $sampleType->id,
            'position' => 5,
        ]);


        $aliquots = [
            'sampleTypes' => json_encode([
                [
                    'id' => $sampleType->id,
                    'quantity' => 3
                ]
            ])
        ];

        $sampleId = Sample::latest()->first()->id;

        $this->put(self::RESOURCE_URI . "/{$sampleId}", array_merge($sample, $aliquots))
            ->assertSuccessful();

        $this->assertDatabaseHas('storage', [
            'study_id' => $user->study_id,
            'sample_type_id' => $sampleType->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('storage', [
            'study_id' => $user->study_id,
            'sample_type_id' => $sampleType->id,
            'position' => 2
        ]);

        $this->assertDatabaseMissing('storage', [
            'study_id' => $user->study_id,
            'sample_type_id' => $sampleType->id,
            'position' => 3
        ]);

        $this->assertDatabaseMissing('storage', [
            'study_id' => $user->study_id,
            'sample_type_id' => $sampleType->id,
            'position' => 4
        ]);

        $aliquots = [
            'sampleTypes' => json_encode([
                [
                    'id' => $sampleType->id,
                    'quantity' => 5
                ]
            ])
        ];

        $this->put(self::RESOURCE_URI . "/{$sampleId}", array_merge($sample, $aliquots))
            ->assertSuccessful();

        $this->assertDatabaseHas('storage', [
            'study_id' => $user->study_id,
            'sample_type_id' => $sampleType->id,
            'position' => 4
        ]);
    }

}
