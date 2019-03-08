<?php

namespace App\Http\Controllers;

use App\Models\SampleType;
use App\Models\Storage;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index(SampleType $sampleType, Request $request, Guard $guard)
    {
        abort_unless($guard->user()->study_id, 401);

        $perPage = $sampleType->columns * $sampleType->rows;
        $plateNumber = $request->plate > 0 ? $request->plate : 1;

        $positions = Storage::with('sample', 'sample.shipments')
            ->where('sample_type_id', $sampleType->id)
            ->take($perPage)
            ->offset(($plateNumber - 1) * $perPage)
            ->get();

        $plate = [];
        $shipments = [];

        for ($row = 0; $row < $sampleType->rows; $row++) {
            $plate[$row] = [];
            for ($column = 0; $column < $sampleType->columns; $column++) {
                $currentIndex = $column +
                    ($row * $sampleType->columns);

                if (isset($positions[$currentIndex])) {
                    $plate[$row][$column] = [
                        'id' => $positions[$currentIndex]->sample->id,
                        'sample_id' => $positions[$currentIndex]->sample->sample_id,
                        'shipped' => $this->isShipped($positions[$currentIndex]->sample, $shipments)
                    ];
                } else {
                    $plate[$row][$column] = [
                        'id' => null,
                        'sample_id' => '',
                        'shipped' => false
                    ];
                }
            }
        }

        return [
            'data' => $plate,
            'size' => [
                'columns' => $sampleType->columns,
                'rows' => $sampleType->rows
            ]
        ];
    }

    public function isShipped($sample, &$shipments)
    {
        return false;
    }
}
