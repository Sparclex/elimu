<?php

namespace Tests\Feature;

use App\Models\Sample;
use App\Models\SampleMutation;
use App\Models\SampleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SampleMutationsTest extends TestCase
{
    use RefreshDatabase;

    private const RESOURCE_URI = '/nova-api/sample-mutations';

    /** @test */
    public function it_monitor_can_view_sample_mutations()
    {
        $this->signInMonitor();

        $this->get(self::RESOURCE_URI)
            ->assertSuccessful();
    }

    /** @test */
    public function a_scientist_can_create_a_sample_mutation()
    {
        $user = $this->signInScientist();

        $sample = factory(Sample::class)->create([
            'study_id' => $user->study_id
        ]);

        $sampleMutation = factory(SampleMutation::class)
            ->raw([
                'sample_id' => $sample->id,
                'quantity' => 0,
                'study_id' => $user->study_id
            ]);

        $this->post(self::RESOURCE_URI, array_merge(
            [
                'sample' => $sampleMutation['sample_id'],
                'sampleType' => $sampleMutation['sample_type_id']
            ],
            $sampleMutation
        ))->assertSuccessful();

        $this->assertDatabaseHas('sample_mutations', $sampleMutation);

    }

    /** @test */
    public function a_monitor_cannot_create_a_sample_mutation()
    {
        $user = $this->signInMonitor();

        $sample = factory(Sample::class)->create([
            'study_id' => $user->study_id
        ]);

        $sampleMutation = factory(SampleMutation::class)
            ->raw([
                'sample_id' => $sample->id,
                'quantity' => 0,
                'study_id' => $user->study_id
            ]);

        $this->post(self::RESOURCE_URI, array_merge(
            [
                'sample' => $sampleMutation['sample_id'],
                'sampleType' => $sampleMutation['sample_type_id']
            ],
            $sampleMutation
        ))->assertForbidden();

        $this->assertDatabaseMissing('sample_mutations', $sampleMutation);
    }

    /** @test */
    public function a_scientist_can_update_a_sample_mutation()
    {
        $this->withoutExceptionHandling();

        $user = $this->signInScientist();

        $sample = factory(Sample::class)->create([
            'study_id' => $user->study_id
        ]);

        $sampleMutation = factory(SampleMutation::class)
            ->create([
                'sample_id' => $sample->id,
                'quantity' => 0,
                'study_id' => $user->study_id
            ]);

        $this->put(self::RESOURCE_URI . "/{$sampleMutation->id}", array_merge(
            [
                'sample' => $sampleMutation['sample_id'],
                'sampleType' => $sampleMutation['sample_type_id']
            ],
            $sampleMutation->toArray()
        ))->assertSuccessful();

        $this->assertDatabaseHas('sample_mutations', $sampleMutation->toArray());
    }

    /** @test */
    public function a_monitor_cannot_update_a_sample_mutation()
    {
        $user = $this->signInMonitor();

        $sample = factory(Sample::class)->create([
            'study_id' => $user->study_id
        ]);

        $sampleMutation = factory(SampleMutation::class)
            ->create([
                'sample_id' => $sample->id,
                'quantity' => 0,
                'study_id' => $user->study_id
            ]);

        $this->put(self::RESOURCE_URI . "/{$sampleMutation->id}", array_merge(
            [
                'sample' => $sampleMutation['sample_id'],
                'sampleType' => $sampleMutation['sample_type_id']
            ],
            $sampleMutation->toArray()
        ))->assertForbidden();
    }

    /** @test */
    public function a_scientist_can_delete_a_sample_mutation()
    {
        $user = $this->signInScientist();

        $sample = factory(Sample::class)->create([
            'study_id' => $user->study_id
        ]);

        $sampleMutation = factory(SampleMutation::class)->create(
            [
                'sample_id' => $sample->id,
                'quantity' => 0,
                'study_id' => $user->study_id
            ]);

        $this->novaDelete(self::RESOURCE_URI, $sampleMutation->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('sample_mutations', $sampleMutation->toArray());
    }

    /** @test */
    public function a_monitor_cannot_delete_a_sample_mutation()
    {
        $user = $this->signInMonitor();

        $sample = factory(Sample::class)->create([
            'study_id' => $user->study_id
        ]);

        $sampleMutation = factory(SampleMutation::class)->create(
            [
                'sample_id' => $sample->id,
                'quantity' => 0,
                'study_id' => $user->study_id
            ]);

        $this->novaDelete(self::RESOURCE_URI, $sampleMutation->id)
            ->assertSuccessful();

        $this->assertDatabaseHas('sample_mutations', $sampleMutation->toArray());
    }

    /** @test */
    public function a_monitor_can_view_a_sample_mutation()
    {
        $user = $this->signInMonitor();

        $sample = factory(Sample::class)->create([
            'study_id' => $user->study_id
        ]);

        $sampleMutation = factory(SampleMutation::class)->create(
            [
                'sample_id' => $sample->id,
                'quantity' => 0,
                'study_id' => $user->study_id
            ]);

        $this->get(self::RESOURCE_URI . "/{$sampleMutation->id}")
            ->assertSuccessful();
    }

    /** @test */
    public function a_sample_should_be_stored_given_a_quantity()
    {
        $this->withoutExceptionHandling();

        $user = $this->signInScientist();

        $sampleType = factory(SampleType::class)->create();
        $sample = factory(Sample::class)->create(['study_id' => $user->study_id]);

        $user->study->sampleTypes()->attach($sampleType, ['rows' => 10, 'columns' => 10]);
        $sampleMutation = factory(SampleMutation::class)
            ->raw([
                'quantity' => 1,
                'sample_type_id' => $sampleType->id,
                'sample_id' => $sample->id,
                'study_id' => $user->id,
            ]);

        $this->post(self::RESOURCE_URI, array_merge(
            [
                'sampleType' => $sampleType->id,
                'sample' => $sample->id,
            ],
            $sampleMutation
        ))
            ->assertSuccessful();

        $this->assertDatabaseHas('sample_mutations', $sampleMutation);

        $this->assertDatabaseHas('storage', [
            'study_id' => $user->study_id,
            'sample_id' => $sample->id,
            'sample_type_id' => $sampleType->id,
            'position' => 0
        ]);

        $this->put(self::RESOURCE_URI . "/1", array_merge(
            $sampleMutation,
            [
                'sampleType' => $sampleType->id,
                'sample' => $sample->id,
                'quantity' => 4
            ]
        ))->assertSuccessful();

        $this->assertDatabaseHas('storage', [
            'position' => 1
        ]);
        $this->assertDatabaseHas('storage', [
            'position' => 2
        ]);
        $this->assertDatabaseHas('storage', [
            'position' => 3
        ]);

        $this->put(self::RESOURCE_URI . "/1", array_merge(
            $sampleMutation,
            [
                'sampleType' => $sampleType->id,
                'sample' => $sample->id,
                'quantity' => 2
            ]
        ))->assertSuccessful();

        $this->assertDatabaseMissing('storage', [
            'position' => 2
        ]);
        $this->assertDatabaseMissing('storage', [
            'position' => 3
        ]);
    }
}
