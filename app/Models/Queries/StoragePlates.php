<?php

namespace App\Models\Queries;

use App\Models\SampleType;
use App\Models\Storage;

class StoragePlates
{
    public static function get($type, $plate = 1)
    {
        $sampleType = is_object($type) ? $type : SampleType::firstOrFail($type);

        $positionsPerPlate = $sampleType->columns * $sampleType->rows;

        $positions = Storage::with('sample', 'sample.shipments')
            ->where('sample_type_id', $type->id)
            ->where('position', '>=', ($plate - 1) * $positionsPerPlate)
            ->where('position', '<=', $plate * $positionsPerPlate)
            ->get()
            ->keyBy('position');

        $data = [];

        for ($row = 0; $row < $sampleType->rows; $row++) {
            $data[$row] = [];
            for ($column = 0; $column < $sampleType->columns; $column++) {
                $currentPosition = (($plate - 1) * $sampleType->columns * $sampleType->rows)
                    + (($sampleType->columns * $row) + $column);
                if ($positions->has($currentPosition)) {
                    $data[$row][$column] = [
                        'id' => $positions->get($currentPosition)->sample->id,
                        'sample_id' => $positions->get($currentPosition)->sample->sample_id,
                        'shipped' => false,
                    ];
                } else {
                    $data[$row][$column] = [
                        'id' => null,
                        'sample_id' => null,
                        'shipped' => false
                    ];
                }
            }
        }

        return $data;
    }
}
