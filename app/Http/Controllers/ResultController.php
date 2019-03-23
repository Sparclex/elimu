<?php

namespace App\Http\Controllers;

use App\Exports\ResultExport;
use App\Models\Assay;
use Illuminate\Contracts\Auth\Guard;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResultController extends Controller
{
    public function index(Assay $assay, NovaRequest $request, Guard $guard)
    {
        abort_unless(
            $guard->user()->study_id
            && $guard->user()->study_id == $assay->study_id,
            403
        );
        $experimentType = config('elimu.result_types.'.$assay->definitionFile->result_type);

        $experimentType = new $experimentType(null, $assay->definitionFile->parameters->keyBy('target'));

        $response = $experimentType->results($request, $assay);

        $response['filters'] = collect($experimentType->filters($request))->map->serialize($request);

        return $response;
    }

    public function targets(Assay $assay, Guard $guard)
    {
        abort_unless(
            $guard->user()->study_id
            && $guard->user()->study_id == $assay->study_id,
            403
        );

        return $assay->definitionFile->parameters->pluck('target');
    }

    public function requestForDownload(Assay $assay, NovaRequest $request, Guard $guard)
    {
        abort_unless(
            $guard->user()->study_id
            && $guard->user()->study_id == $assay->study_id,
            403
        );

        return [
            'download' => route('download-results', compact('assay')),
            'name' => 'results.xlsx',
        ];
    }

    public function download(Assay $assay, NovaRequest $request, Guard $guard)
    {
        abort_unless(
            $guard->user()->study_id
            && $guard->user()->study_id == $assay->study_id,
            403
        );

        $experimentType = config('elimu.result_types.'.$assay->definitionFile->result_type);

        $experimentType = new $experimentType(null, $assay->definitionFile->parameters->keyBy('target'));

        return (new ResultExport($experimentType, $assay))->download('results.xlsx');
    }
}
