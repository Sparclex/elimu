<?php

namespace App\Support;

use App\Models\Storage;

class SampleTypeStorage
{
    protected $lastPosition;
    protected $sampleTypeId;
    protected $studyId;

    public function __construct($sampleTypeId, $studyId)
    {

        $this->sampleTypeId = $sampleTypeId;
        $this->studyId = $studyId;
    }

    public function getNextPosition()
    {
        $this->lastPosition += 1;

        return $this->lastPosition;
    }

    public function getLastPosition()
    {
        if (!$this->lastPosition) {
            $this->lastPosition = Storage::where('study_id', $this->studyId)
                ->where('sample_type_id', $this->sampleTypeId)
                ->orderByDesc('position')
                ->pluck('position');
        }

        return $this->lastPosition;
    }

    public function store($sampleId, $quantity = null, $persist = true)
    {
        $newPositions = [];

        if (is_array($sampleId)) {
            $persist = $quantity ?? true;

            foreach ($sampleId as $id => $quantity) {
                $newPositions += $this->store($id, $quantity, $persist);
            }
        } else {
            for ($i = 0; $i < $quantity; $i++) {
                $newPositions[] = $this->add($sampleId, $persist);
            }
        }

        return $newPositions;
    }

    public function add($sampleId, $persist = true)
    {
        $newPosition = [
            'sample_id' => $sampleId,
            'sample_type_id' => $this->sampleTypeId,
            'study_id' => $this->studyId,
            'position' => $this->getNextPosition()
        ];

        if ($persist) {
            Storage::forceCreate($newPosition);
        }

        return $newPosition;
    }
}
