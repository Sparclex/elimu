<?php

namespace Tests\Setup;

use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Storage;
use App\Models\Study;
use App\Policies\Authorization;
use App\Support\StoragePointer;
use Facades\Tests\Setup\StudyFactory;
use Illuminate\Database\Eloquent\Collection;

class StorageFactory
{
    public $monitor;

    public $type;

    public function withMonitor($monitor)
    {
        $this->monitor = $monitor;

        return $this;
    }

    public function withType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function create($quantity = 1)
    {
        $study = StudyFactory::withMonitor($this->monitor)->create();
        $typeId = $this->type ? $this->type->id : factory(SampleType::class)->create()->id;

        $sampleTypeStorage = new StoragePointer($typeId, $study->id);

        $storages = new Collection();
        for($i = 0; $i < $quantity; $i++) {
            $storages->push($sampleTypeStorage->add(factory(Sample::class)->create(['study_id' => $study->id])->id));
        }

        return $storages->count() == 1 ? $storages->first() : $storages;

    }
}