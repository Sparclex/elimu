<?php

namespace App\Http\Controllers;

use App\Models\Assay;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ResultController extends Controller
{
    public function index(Assay $assay, Request $request, Guard $guard)
    {
        abort_unless($guard->user()->study_id && $guard->user()->study_id == $assay->study_id, 403);

        $experimentType = config('lims.result_types.' . $assay->definitionFile->result_type);

        $experimentType = new $experimentType(null, $assay->definitionFile->parameters->keyBy('target'));

        return $experimentType->results([
            'perPage' => $request->get('perPage', 25),
            'assay_id' => $assay->id,
            'target' => $request->get('target'),
            'status' => $request->get('status'),
        ]);
    }

    public function targets(Assay $assay, Guard $guard)
    {
        abort_unless($guard->user()->study_id && $guard->user()->study_id == $assay->study_id, 403);

        return $assay->definitionFile->parameters->pluck('target');
    }
}
