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
        if (!$guard->user()->study_id) {
            abort(401);
        }

        $sampleType = $guard
            ->user()
            ->study
            ->sampleTypes()
            ->wherePivot('sample_type_id', $sampleType->id)
            ->first();

        $perPage = $sampleType->pivot->columns * $sampleType->pivot->rows;
        $plateNumber = $request->plate > 0 ? $request->plate : 1;

        $positions = Storage::with('sample', 'sample.shipments')
            ->take($perPage)
            ->offset(($plateNumber - 1) * $perPage)
            ->get();

        $plate = [];
        $shipments = [];

        for ($row = 0; $row < $sampleType->pivot->rows; $row++) {
            $plate[$row] = [];
            for ($column = 0; $column < $sampleType->pivot->columns; $column++) {
                $currentIndex = $column +
                    ($row * $sampleType->pivot->columns);

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
                'columns' => $sampleType->pivot->columns,
                'rows' => $sampleType->pivot->rows
            ]
        ];
    }

    public function isShipped($sample, &$shipments)
    {
        return false;
    }
}
