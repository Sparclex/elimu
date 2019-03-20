<?php

namespace App\Http\Controllers;

use App\Models\Queries\StoragePlates;
use App\Models\SampleType;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function storageable(Guard $guard)
    {
        abort_unless($guard->user()->study_id, 401);

        return SampleType::whereNotNull('columns')
            ->whereNotNull('rows')
            ->get()
            ->map(function ($sampleType) {
                return [
                    'id' => $sampleType->id,
                    'name' => $sampleType->name
                ];
            });
    }

    public function index(SampleType $sampleType, Request $request, Guard $guard)
    {
        abort_unless($guard->user()->study_id, 401);

        $plate = StoragePlates::get($sampleType, $request->get('plate'));

        return [
            'data' => $plate,
            'size' => [
                'columns' => $sampleType->columns,
                'rows' => $sampleType->rows,
                'columnFormat' => $sampleType->column_format,
                'rowFormat' => $sampleType->row_format
            ]
        ];
    }

    public function isShipped($sample, &$shipments)
    {
        return false;
    }
}
