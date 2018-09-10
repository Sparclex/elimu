<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Models\SampleInformation;
use App\Models\SampleType;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;

class SampleBatchImportController extends Controller
{
    use ValidatesRequests;

    public function handle(Request $request)
    {
        $data = $this->validate(
            $request, [
            'study' => 'required|exists:studies,id',
            'samples.*.sampleId' => 'required',
            'samples.*.subjectId' => 'required',
            'samples.*.collectionDate' => 'required|date_format:Y-m-d H:i',
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
        $samples = collect($data['samples']);
        $sampleTypes = $samples->pluck('sampleType')->map(function($item) {
            return trim($item);
        })->unique();
        $newSampleTypes = $sampleTypes->diff(
            SampleType::whereIn('name', $sampleTypes->toArray())->pluck('name'));
        foreach ($newSampleTypes as $type) {
            SampleType::create(['name' => $type]);
        }
        $storedSampleTypes = SampleType::pluck('id', 'name');
        foreach ($samples as $sample) {
            $sampleInformations = SampleInformation::firstOrCreate(
                [
                    'sample_id' => $sample['sampleId'],
                ], [
                    'subject_id' => $sample['subjectId'],
                    'collected_at' => Carbon::createFromFormat('Y-m-d H:i', $sample['collectionDate']),
                    'visit_id' => $sample['visitId']
                ]);
            Sample::firstOrCreate(
                [
                    'sample_type_id' => $storedSampleTypes[$sample['sampleType']],
                    'sample_information_id' => $sampleInformations->id,
                    'study_id' => $data['study'],
                ], [
                    'quantity' => $sample['quantity'],
                ]);
        }

        return [
            'message' => count($samples)." samples successfully imported",
            'sampleTypes' => $storedSampleTypes,
        ];
    }
}
