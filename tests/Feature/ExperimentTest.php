<?php

namespace Tests\Feature;

use App\Models\Assay;
use App\Models\Experiment;
use App\Models\Sample;
use App\Models\SampleMutation;
use App\Models\SampleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperimentTest extends TestCase
{
    use RefreshDatabase;

    private const RESOURCE_URI = '/nova-api/experiments';

    /** @test */
    public function a_monitor_can_view_experiments()
    {
        $this->signInMonitor();

        $this->get(self::RESOURCE_URI)
            ->assertSuccessful();
    }

    /** @test */
    public function a_monitor_can_view_an_experiment()
    {
        $this->withoutExceptionHandling();

        $user = $this->signInMonitor();

        $experiment = factory(Experiment::class)->create(['study_id' => $user->study_id]);

        $this->get(self::RESOURCE_URI . "/{$experiment->id}")
            ->assertSuccessful();
    }

    /** @test */
    public function a_scientist_can_create_a_experiment()
    {
        $this->withoutExceptionHandling();

        $user = $this->signInScientist();

        $sampleType = factory(SampleType::class)->create();

        $samples = factory(SampleMutation::class, 3)->create(
            [
                'sample_type_id' => $sampleType->id,
                'study_id' => $user->study_id,
                'sample_id' => function () use ($user) {
                    return factory(Sample::class)->create(['study_id' => $user->study_id])->id;
                }
            ]
        );

        $this->postJson(self::RESOURCE_URI, array_merge(
                [
                    'sampleType' => $sampleType->id,
                    'assay' => factory(Assay::class)->create()->id,
                    'samples' => $samples->pluck('sample')->pluck('sample_id')->implode("\n")
                ])
        )->assertSuccessful();

        $this->assertDatabaseHas('requested_experiments', [
            'sample_id' => $samples->pluck('sample')->first()->id,
            'experiment_id' => 1
        ]);
        $this->assertDatabaseHas('requested_experiments', [
            'sample_id' => $samples->pluck('sample')[1]->id,
            'experiment_id' => 1
        ]);
        $this->assertDatabaseHas('requested_experiments', [
            'sample_id' => $samples->pluck('sample')->last()->id,
            'experiment_id' => 1
        ]);
    }

    /** @test */
    public function a_monitor_cannot_create_an_experiment()
    {
        $user = $this->signInMonitor();

        $sampleType = factory(SampleType::class)->create();

        $samples = factory(SampleMutation::class, 3)->create(
            [
                'sample_type_id' => $sampleType->id,
                'study_id' => $user->study_id,
                'sample_id' => function () use ($user) {
                    return factory(Sample::class)->create(['study_id' => $user->study_id])->id;
                }
            ]
        );

        $this->postJson(self::RESOURCE_URI, array_merge(
                [
                    'sampleType' => $sampleType->id,
                    'assay' => factory(Assay::class)->create()->id,
                    'samples' => $samples->pluck('sample')->pluck('sample_id')->implode("\n")
                ])
        )->assertForbidden();

        $this->assertDatabaseMissing('requested_experiments', [
            'sample_id' => $samples->pluck('sample')->first()->id,
            'experiment_id' => 1
        ]);
    }

    /** @test */
    public function a_scientist_can_update_an_experiment()
    {
        $user = $this->signInScientist();

        $experiment = factory(Experiment::class)->create(['study_id' => $user->study_id]);

        $this->putJson(self::RESOURCE_URI . "/{$experiment->id}", array_merge(
            $experiment->toArray(),
            [
                'sampleType' => $experiment->sample_type_id,
                'assay' => $experiment->assay_id
            ]
        ))
            ->assertSuccessful();
    }

    /** @test */
    public function a_monitor_cannot_update_an_experiment()
    {
        $user = $this->signInMonitor();

        $experiment = factory(Experiment::class)->create(['study_id' => $user->study_id]);

        $this->putJson(self::RESOURCE_URI . "/{$experiment->id}", array_merge(
            $experiment->toArray(),
            [
                'sampleType' => $experiment->sample_type_id,
                'assay' => $experiment->assay_id
            ]
        ))
            ->assertForbidden();
    }

    /** @test */
    public function a_scientist_can_delete_an_experiment()
    {
        $user = $this->signInScientist();

        $experiment = factory(Experiment::class)->create(['study_id' => $user->study_id]);

        $this->novaDelete(self::RESOURCE_URI, $experiment->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('experiments', $experiment->toArray());
    }

    /** @test */
    public function a_monitor_cannot_delete_an_experiment()
    {
        $user = $this->signInMonitor();

        $experiment = factory(Experiment::class)->create(['study_id' => $user->study_id]);

        $this->novaDelete(self::RESOURCE_URI, $experiment->id)
            ->assertSuccessful();

        $this->assertDatabaseHas('experiments', $experiment->toArray());
    }
}
