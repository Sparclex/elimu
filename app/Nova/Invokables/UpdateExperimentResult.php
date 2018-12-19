<?php

namespace App\Nova\Invokables;

use App\Models\Experiment;
use App\ResultHandlers\ResultHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateExperimentResult
{
    public function __invoke(Request $request, Experiment $model)
    {
        $resultFile = $request->result_file->store('experiments');

        $this->handleResults(new $model->result_handler($model, $request->result_file));

        return [
            'result_file' => $resultFile,
            'original_filename' => $request->result_file->getClientOriginalName(),
        ];
    }

    private function handleResults(ResultHandler $resultHandler)
    {
        $resultHandler->handle();
    }
}
