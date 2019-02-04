<?php

namespace App\Queries;

use App\Models\Storage;

class StoragePlates
{
    public static function get($type, $plate = 1)
    {
        $sampleTypeId = is_object($type) ? $type->id : $type;

        $sampleType = auth()
            ->user()
            ->study
            ->sampleTypes()
            ->wherePivot('sample_type_id', $sampleTypeId)
            ->first();

        $positionsPerPlate = $sampleType->pivot->columns * $sampleType->pivot->rows;

        $positions = Storage::with('sample', 'sample.shipments')
            ->take($positionsPerPlate)
            ->offset(($plate - 1) * $positionsPerPlate)
            ->get();

        return array_chunk(
            $positions->map(function ($position) {
                return [
                    'id' => $position->sample->id,
                    'sample_id' => $position->sample->sample_id,
                    'shipped' => false,
                ];
            })->concat(array_fill(0, $positionsPerPlate - $positions->count(), [
                'id' => null,
                'sample_id' => null,
                'shipped' => null
            ]))
                ->toArray(),
            $sampleType->pivot->columns,
            false
        );
    }
}
