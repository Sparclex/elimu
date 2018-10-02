<?php

namespace App\Http\Controllers;

use App\FileTypes\RDML;
use App\Models\SampleData;
use Illuminate\Http\File;
use Illuminate\Http\Request;

class SampleDataController extends Controller
{
    public function handle(SampleData $dataSample, Request $request)
    {
        if ($dataSample->experiment->result_type != 'qPcr Rdml') {
            return [
                'display' => false
            ];
        }
        $sampleId = $dataSample->sample->sampleInformation->sample_id;
        $position = $dataSample->secondary_value;

        $parameters = $dataSample->experiment->inputParameters;

        $file = new File(storage_path('app/'.$dataSample->experiment->result_file));
        $rdml = RDML::make($file)->withInputParameters($parameters);
        return [
            'display' => true,
            'data' => $rdml->getChartDataFor($sampleId, $position, $dataSample->target)
        ];
    }
}
