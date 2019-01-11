<?php

namespace Tests\Feature;

use App\Models\Study;
use App\Policies\Authorization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyTest extends TestCase
{
    use RefreshDatabase;

    private const RESOURCE_URI = '/nova-api/studies';

    /** @test */
    public function an_administrator_can_manage_a_study()
    {
        $this->withoutExceptionHandling();
        $this->signInAsAdministrator();

        $this->get(self::RESOURCE_URI)
            ->assertSuccessful();

        $this->post(self::RESOURCE_URI, factory(Study::class)->raw())
            ->assertSuccessful();

        $study = factory(Study::class)->create();
        $study->name = $study->name . 'new name';

        $this->put(self::RESOURCE_URI . "/{$study->id}", $study->toArray())
            ->assertSuccessful();

        $this->novaDelete(self::RESOURCE_URI, $study->id)
            ->assertSuccessful();
    }

    /** @test */
    public function a_lab_manager_can_only_edit_his_study()
    {
        $user = $this->signIn();
        [$study, $otherStudy] = factory(Study::class, 2)->create();

        $user->studies()->attach($study, ['power' => Authorization::LABMANAGER]);
        $user->study()->associate($study);

        $this->get(self::RESOURCE_URI)
            ->assertSuccessful();

        $this->get(self::RESOURCE_URI . "/{$study->id}")
            ->assertSuccessful();

        $study->name = $study->name . 'new name';

        $this->put(self::RESOURCE_URI . "/{$study->id}", $study->toArray())
            ->assertSuccessful();

        $this->novaDelete(self::RESOURCE_URI, $study->id)
            ->assertSuccessful();

        $this->assertDatabaseHas('studies', [
            'id' => $study->id
        ]);

        $this->get(self::RESOURCE_URI . "/{$otherStudy->id}")
            ->assertForbidden();

        $this->put(self::RESOURCE_URI . "/{$otherStudy->id}", $otherStudy->toArray())
            ->assertForbidden();
    }

    /** @test */
    public function a_normal_user_cannot_manage_a_study()
    {
        $this->signIn();

        $this->get('nova')->assertSuccessful();

        $this->get(self::RESOURCE_URI)
            ->assertForbidden();

        $this->post(self::RESOURCE_URI, $study = factory(Study::class)->raw())
            ->assertForbidden();

        $study = factory(Study::class)->create();
        $study->name = $study->name . 'new name';

        $this->put(self::RESOURCE_URI . "/{$study->id}", $study->toArray())
            ->assertForbidden();

        $this->novaDelete(self::RESOURCE_URI, $study->id)
            ->assertForbidden();
    }
}
