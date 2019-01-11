<?php

namespace Tests\Feature;

use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SamplesTest extends TestCase
{
    use RefreshDatabase;

    private const RESOURCE_URI = '/nova-api/samples';

    /** @test */
    public function a_scientist_can_manage_a_sample()
    {
        $this->withoutExceptionHandling();

        $user = $this->signInScientist();

        $this->get(self::RESOURCE_URI)->assertSuccessful();

        $sample = factory(Sample::class)->raw(['study_id' => $user->study->id]);

        $this->post(self::RESOURCE_URI, $sample)
            ->assertSuccessful();

        $sample = factory(Sample::class)->create(['study_id' => $user->study->id]);

        $this->get(self::RESOURCE_URI . "/{$sample->id}")
            ->assertSuccessful();

        $this->put(self::RESOURCE_URI . "/{$sample->id}", $sample->toArray())
            ->assertSuccessful();

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
}
