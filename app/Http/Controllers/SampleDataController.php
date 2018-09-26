<?php

namespace App\Http\Controllers;

use App\Models\SampleData;
use App\ResultHandlers\Rdml\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SampleDataController extends Controller
{
    public function handle(SampleData $dataSample, Request $request)
    {
        $sampleId = $dataSample->sample->sampleInformation->sample_id;
        $position = $dataSample->secondary_value;

        $parameters = $dataSample->experiment->assay->inputParameters()->first()->parameters->mapWithKeys(function ($row
        ) {
            return [$row['target'] => $row['threshold']];
        });

        $processor = new Processor(Storage::get($dataSample->experiment->file), $parameters->toArray());
        return $processor->getChartDataFor($sampleId, $position, $dataSample->target);
    }
}
