<?php

namespace Tests\Feature;

use App\Models\Study;
use Facades\Tests\Setup\StudyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyTest extends TestCase
{
    use RefreshDatabase;

    private const RESOURCE_URI = '/nova-api/studies';

    /** @test */
    public function an_administrator_can_view_any_study()
    {
        $this->signInAsAdministrator();

        $this->get(self::RESOURCE_URI . '/' . factory(Study::class)->create()->id)
            ->assertSuccessful();
    }

    /** @test */
    public function an_administrator_can_create_a_study()
    {
        $this->signInAsAdministrator();

        $this->get(self::RESOURCE_URI)
            ->assertSuccessful();

        $this->post(self::RESOURCE_URI, factory(Study::class)->raw())
            ->assertSuccessful();
    }

    /**
     * @test
     */
    public function an_administrator_can_update_a_study()
    {
        $this->signInAsAdministrator();

        $study = factory(Study::class)->create();
        $study->name = $study->name . 'new name';

        $this->put(self::RESOURCE_URI . "/{$study->id}", $study->toArray())
            ->assertSuccessful();
    }

    /**
     * @test
     */
    public function an_administrator_can_delete_a_study()
    {
        $this->signInAsAdministrator();
        $study = factory(Study::class)->create();

        $this->novaDelete(self::RESOURCE_URI, $study->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('studies', $study->toArray());
    }

    /** @test */
    public function a_lab_manager_can_only_view_his_study()
    {
        $study = StudyFactory::withManager($user = $this->signIn())->create();

        $this->get(self::RESOURCE_URI . "/{$study->id}")
            ->assertSuccessful();

        $notHisStudy = factory(Study::class)->create();

        $this->get(self::RESOURCE_URI . "/{$notHisStudy->id}")
            ->assertForbidden();
    }

    /** @test */
    public function a_lab_manager_can_update_his_study()
    {
        $study = StudyFactory::withManager($user = $this->signIn())->create();

        $this->get(self::RESOURCE_URI)
            ->assertSuccessful();

        $this->get(self::RESOURCE_URI . "/{$study->id}")
            ->assertSuccessful();

        $study->name = $study->name . 'new name';

        $this->put(self::RESOURCE_URI . "/{$study->id}", $study->toArray())
            ->assertSuccessful();
    }

    /** @test */
    public function a_lab_manager_cannot_delete_his_study()
    {
        $study = StudyFactory::withManager($user = $this->signIn())->create();

        $this->novaDelete(self::RESOURCE_URI, $study->id)
            ->assertSuccessful();

        $this->assertDatabaseHas('studies', [
            'id' => $study->id
        ]);
    }

    /** @test */
    public function a_lab_manager_cannot_update_another_study()
    {
        $this->signIn();

        $study = StudyFactory::create();

        $this->get(self::RESOURCE_URI . "/{$study->id}")
            ->assertForbidden();

        $this->put(self::RESOURCE_URI . "/{$study->id}", $study->toArray())
            ->assertForbidden();
    }

    /** @test */
    public function a_normal_user_cannot_view_any_studies()
    {
        $this->signIn();

        $this->get(config('nova.path'))->assertSuccessful();

        $this->get(self::RESOURCE_URI)
            ->assertForbidden();
    }

    /** @test */
    public function a_normal_user_cannot_create_a_study()
    {
        $this->signIn();

        $this->post(self::RESOURCE_URI, $study = factory(Study::class)->raw())
            ->assertForbidden();
    }

    /** @test */
    public function a_normal_user_cannot_update_a_study()
    {
        $this->signIn();

        $study = factory(Study::class)->create();
        $study->name = $study->name . 'new name';

        $this->put(self::RESOURCE_URI . "/{$study->id}", $study->toArray())
            ->assertForbidden();

        $this->novaDelete(self::RESOURCE_URI, $study->id)
            ->assertForbidden();
    }
}
