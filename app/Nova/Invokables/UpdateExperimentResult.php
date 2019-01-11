<?php

namespace App\Nova\Invokables;

use App\Models\Experiment;
use App\ResultHandlers\ResultHandler;
use Illuminate\Http\Request;

class UpdateExperimentResult
{
    public function __invoke(Request $request, Experiment $model)
    {

        $resultFile = $request->result_file->storeAs('experiments', time() . "."
            . $request->result_file->getClientOriginalExtension());

        $this->handleResults(new $model->result_handler($model, $resultFile));

        $deleteFile = new DeleteExperimentFile();
        $deleteFile($request, $model);

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
