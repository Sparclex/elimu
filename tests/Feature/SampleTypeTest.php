<?php

namespace Tests\Feature;

use App\Models\SampleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SampleTypeTest extends TestCase
{
    use RefreshDatabase;

    private const RESOURCE_URI = '/nova-api/sample-types';

    /** @test */
    public function a_monitor_can_view_any_type()
    {
        $this->withoutExceptionHandling();
        $this->signInMonitor();

        $this->get(self::RESOURCE_URI)
            ->assertSuccessful();

        $this->get(self::RESOURCE_URI . '/' . factory(SampleType::class)->create()->id)
            ->assertSuccessful();
    }

    /** @test */
    public function a_monitor_cannot_create_a_new_type()
    {
        $this->signInMonitor();

        $this->post(self::RESOURCE_URI, factory(SampleType::class)->raw())
            ->assertForbidden();
    }

    /** @test */
    public function a_monitor_cannot_update_a_type()
    {
        $this->signInMonitor();

        $type = factory(SampleType::class)->create();

        $this->put(self::RESOURCE_URI . "/{$type->id}", array_merge($type->toArray(), ['name' => 'New']))
            ->assertForbidden();
    }

    /** @test */
    public function a_scientist_can_create_a_new_type()
    {
        $this->signInScientist();

        $this->post(self::RESOURCE_URI, factory(SampleType::class)->raw())
            ->assertSuccessful();
    }

    /** @test */
    public function a_scientist_can_update_a_type()
    {
        $this->signInScientist();

        $type = factory(SampleType::class)->create();

        $this->put(self::RESOURCE_URI . "/{$type->id}", array_merge($type->toArray(), ['name' => 'New']))
            ->assertSuccessful();
    }

    /** @test */
    public function a_scientist_cannot_delete_a_type()
    {
        $this->signInScientist();

        $type = factory(SampleType::class)->create();

        $this->novaDelete(self::RESOURCE_URI, $type->id)
            ->assertSuccessful();

        $this->assertDatabaseHas('sample_types', $type->toArray());
    }

    /** @test */
    public function a_lab_manager_can_delete_a_study()
    {
        $this->signInManager();
        $type = factory(SampleType::class)->create();

        $this->novaDelete(self::RESOURCE_URI, $type->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('sample_types', $type->toArray());
    }
}