<?php

namespace Sparclex\Lims\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Sparclex\Lims\Models\SampleInformation;
use Sparclex\Lims\Models\SampleType;

class SampleBatchImportController extends Controller
{
    use ValidatesRequests;

    public function handle(Request $request)
    {
        $samples = $request->get('samples');
        abort_if(! $samples, 422, 'No data given');
        $this->validate(
            $request, [
            'samples.*.sampleId' => 'required',
            'samples.*.subjectId' => 'required',
            'samples.*.collectionDate' => 'required|date',
            'samples.*.visitId' => 'required|string',
            'samples.*.quantity' => 'nullable|numeric',
            'samples.*.sampleType' => 'required|string',
        ], [
            'samples.*.sampleid' => [
                'required' => 'Column sampleId is missing',
            ],
            'samples.*.subjectId' => [
                'required' => 'Column subjectId',
            ],
            'samples.*.collectionDate' => [
                'required' => 'Column collectionDate is missing',
            ],
            'samples.*.visitId' => [
                'required' => 'Column visitId is missing',
            ],
            'samples.*.quantity' => [
                'numeric' => 'Column quantity must only contain a number',
            ],
            'samples.*.sampleType' => [
                'required' => 'Column sampleType is missing',
            ],
        ]);
        $samples = collect($samples);
        // sampleid, subjectid, collectiondate, visitId, quantity, sampleType
        $sampleTypes = $samples->pluck('type')->map('trim');
        $newSampleTypes = $sampleTypes->diff(
            SampleType::whereIn('name', $sampleTypes)->pluck('name'));
        foreach ($newSampleTypes as $type) {
            SampleType::create(['name' => $type]);
        }
        $storedSampleTypes = SampleType::pluck('name', 'id');
        foreach ($samples as $sample) {
            $sampleInformations = SampleInformation::firstOrCreate(
                [
                    'sample_id' => $sample['sampleid'],
                ],
                [
                    'subject_id' => $sample['subjectid'],
                    'collected_at' => $sample['collectiondate'],
                    'visit_id' => $sample['visitId'],
                    'quantity' => $sample['quantity']
                ]
                )->first();
            /*
                'subject_id' => $sample['subjectid'],
                'collected_at' => $sample['collectiondate'],
                'visit_id' => $sample['visitId'],
                'quantity' => $sample['quantity'],
            */
            //SampleInformation::firstOrCreate(
            //    ['sample_id' => $sample['sampleId']]], []);
        }

        return [
            'message' => count($samples)."samples successfully imported",
            'sampleTypes' => $storedSampleTypes,
        ];
    }
}
