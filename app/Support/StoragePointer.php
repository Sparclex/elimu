<?php

namespace App\Support;

use App\Models\Storage;

class StoragePointer
{
    protected $position;
    protected $sampleTypeId;
    protected $studyId;

    public function __construct($sampleTypeId, $studyId = null)
    {

        $this->sampleTypeId = $sampleTypeId;
        $this->studyId = $studyId ?? auth()->user()->study_id;
        $this->retrieveLatestPosition();
    }

    protected function retrieveLatestPosition()
    {
        if ($this->position == null) {
            $this->position = Storage::withoutGlobalScopes()
                    ->where('study_id', $this->studyId)
                    ->where('sample_type_id', $this->sampleTypeId)
                    ->orderByDesc('position')
                    ->pluck('position')
                    ->first() ?? -1;
        }

        return $this->position;
    }

    public function store($sampleId, $quantity = null, $persist = true)
    {
        $sampleId = is_object($sampleId) ? $sampleId->id : $sampleId;

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
        $this->next();
        $newPosition = [
            'sample_id' => $sampleId,
            'sample_type_id' => $this->sampleTypeId,
            'study_id' => $this->studyId,
            'position' => $this->getPosition()
        ];

        if ($persist) {
            return Storage::forceCreate($newPosition);
        }

        return $newPosition;
    }

    public function next()
    {
        $this->position = $this->position + 1;
    }

    public function getPosition()
    {
        return $this->position;
    }
}
