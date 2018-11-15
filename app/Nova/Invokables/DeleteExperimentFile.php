<?php

namespace App\Nova\Invokables;

use App\ResultHandlers\ResultHandler;
use Illuminate\Support\Facades\Storage;

class DeleteExperimentFile
{
    public function __invoke($request, $model)
    {
        if (!$model->result_file) {
            return;
        }
        ResultHandler::removeSampleData($model->id);
        Storage::disk('local')->delete($model->result_file);
        return [
            'result_file' => null,
            'original_filename' => null,
            'result_type' => null
        ];
    }
}
