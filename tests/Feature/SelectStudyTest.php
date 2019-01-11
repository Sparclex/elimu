<?php

namespace Tests\Feature;

use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelectStudyTest extends TestCase
{
    use RefreshDatabase;

    private const SELECT_STUDY_URI = '/nova-vendor/lims/studies';

    /** @test */
    public function a_guest_cannot_select_any_studies()
    {
        $study = factory(Study::class)->create();

        $this->get(self::SELECT_STUDY_URI)
            ->assertRedirect();

        $this->post(self::SELECT_STUDY_URI . "/{$study->id}/select")
            ->assertRedirect();
    }

    /** @test */
    public function a_user_can_only_select_assigned_studies()
    {
        [$study, $otherStudy] = factory(Study::class, 2)->create();

        $this->signIn()->studies()->attach($study);

        $this->getJson(self::SELECT_STUDY_URI)
            ->assertSuccessful()
            ->assertJsonFragment(
                ['id' => $study->id]
            )
            ->assertJsonMissing(
                ['id' => $otherStudy->id]
            );

        $this->post(self::SELECT_STUDY_URI . "/{$study->id}/select")
            ->assertSuccessful();

        $this->post(self::SELECT_STUDY_URI . "/{$otherStudy->id}/select")
            ->assertForbidden();
    }
}
