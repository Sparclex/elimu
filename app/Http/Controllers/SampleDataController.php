<?php

namespace App\Http\Controllers;

use App\Models\DataSample;
use App\ResultHandlers\Rdml\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SampleDataController extends Controller
{
    public function handle(DataSample $dataSample, Request $request) {
        $sampleId = $dataSample->sample->sampleInformation->sample_id;
        $position = $dataSample->additional['well'];

        $parameters = $dataSample->data()->first()->experiment->assay->inputParameters()->first()->parameters->mapWithKeys(function($row) {
            return [$row['target'] => $row['threshold']];
        });

        $processor = new Processor(Storage::get($dataSample->data()->first()->file), $parameters->toArray());
        return $processor->getChartDataFor($sampleId, $position, $dataSample->target);
    }
}
