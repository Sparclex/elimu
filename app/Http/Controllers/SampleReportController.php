<?php

namespace App\Http\Controllers;

use App\Models\Experiment;
use App\Models\SampleMutation;
use Illuminate\Http\Request;
use Laravel\Nova\Actions\Action;

class SampleReportController extends Controller
{
    public function downloadLink(SampleMutation $sample, Request $request)
    {
        $this->validate($request, [
            'experiment' => 'exists:sample_data,experiment_id,sample_id,' . $sample->id . ',status,Accepted'
        ]);
        $experiment = Experiment::findOrFail($request->experiment);
        return Action::download(route('report-download', compact('experiment', 'sample')), 'experiment-report.pdf');
    }

    public function download(SampleMutation $sample, Experiment $experiment)
    {
        $sampleData = $sample->data()->where('experiment_id', $experiment->id)->get();
        abort_if(!$sampleData, 404);
        return \PDF::loadView('pdfs.sample-report', compact('sample', 'sampleData', 'experiment'))
            ->setPaper('a4', 'landscape')
            ->stream('experiment-report.pdf');
    }
}
