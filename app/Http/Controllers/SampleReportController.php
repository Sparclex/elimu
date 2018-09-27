<?php

namespace App\Http\Controllers;

use App\Models\Experiment;
use App\Models\Sample;
use Illuminate\Http\Request;
use Laravel\Nova\Actions\Action;

class SampleReportController extends Controller
{
    public function downloadLink(Sample $sample, Request $request)
    {
        $this->validate($request, [
            'experiment' => 'exists:sample_data,experiment_id,sample_id,' . $sample->id . ',status,Accepted'
        ]);
        $experiment = Experiment::findOrFail($request->experiment);
        return Action::download(route('report-download', compact('experiment', 'sample')), 'report.pdf');
    }

    public function download(Sample $sample, Experiment $experiment)
    {
        $sampleData = $sample->data()->where('experiment_id', $experiment->id)->get();
        abort_if(!$sampleData, 404);
        return \PDF::loadView('pdfs.sample-report', compact('sample', 'sampleData', 'experiment'))
            ->setPaper('a4', 'landscape')
            ->stream('experiment-report_sample-' . $sample->sampleInformation->sample_id
                . '_experiment-' . $experiment->reagent->assay->name . '-' . $experiment->id . '.pdf');
    }
}
