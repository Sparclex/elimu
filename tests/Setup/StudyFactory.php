<?php

namespace Tests\Setup;

use App\Models\Study;
use App\Policies\Authorization;
use App\User;

class StudyFactory
{
    /**
     * @var User
     */
    public $manager;

    /**
     * @var User
     */
    public $scientist;

    public function withManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    public function withScientist($scientist)
    {
        $this->scientist = $scientist;

        return $this;
    }

    /**
     * @return Study
     */
    public function create($attributes = [])
    {
        $study = factory(Study::class)->create($attributes);

        if($this->manager) {
            $study->users()->attach($this->manager, ['power' => Authorization::LABMANAGER]);
            $this->manager->study()->associate($study);
        }

        if($this->scientist) {
            $study->users()->attach($this->scientist, ['power' => Authorization::SCIENTIST]);
            $this->scientist->study()->associate($study);
        }

        return $study;
    }
}