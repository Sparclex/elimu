<?php

namespace Tests\Setup;

use App\Models\SampleType;
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

    /**
     * @var User
     */
    public $monitor;

    /**
     * @var array
     */
    public $storage = [];



    public function withMonitor($monitor)
    {
        $this->monitor = $monitor;

        return $this;
    }

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

    public function withStorage($rows = 12, $columns = 8, $sampleType = null)
    {
        $sampleTypeId = $sampleType ? $sampleType->id : factory(SampleType::class)->create()->id;
        $this->storage[$sampleTypeId] = compact('rows', 'columns');

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

        if($this->monitor) {
            $study->users()->attach($this->monitor, ['power' => Authorization::MONITOR]);
            $this->monitor->study()->associate($study);
        }

        if(count($this->storage)) {
            $study->sampleTypes()->attach($this->storage);
        }

        return $study;
    }
}