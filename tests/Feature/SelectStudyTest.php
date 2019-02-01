<?php

namespace Tests\Feature;

use App\Models\Study;
use Facades\Tests\Setup\StudyFactory;
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
        $assignedStudy = StudyFactory::withScientist($this->signIn())->create();
        $notAssignedStudy = factory(Study::class)->create();

        $this->getJson(self::SELECT_STUDY_URI)
            ->assertSuccessful()
            ->assertJsonFragment(
                ['id' => $assignedStudy->id]
            )
            ->assertJsonMissing(
                ['id' => $notAssignedStudy->id]
            );

        $this->post(self::SELECT_STUDY_URI . "/{$assignedStudy->id}/select")
            ->assertSuccessful();

        $this->post(self::SELECT_STUDY_URI . "/{$notAssignedStudy->id}/select")
            ->assertForbidden();
    }
}
