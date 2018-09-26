<?php

namespace App\Observers;

use App\ResultHandlers\Rdml\Extractor;
use App\ResultHandlers\Rdml\Processor;
use Illuminate\Support\Facades\Storage;

class ExtractSampleData
{
    public function __construct($model)
    {
        if(!$model->file) {
            return;
        }
        $parameters = $model->input_parameters;
        $thresholds = $parameters->mapWithKeys(function($row) {
            return [
                $row['target'] => $row['threshold']
            ];
        });
        $extractor = new Extractor(new Processor(Storage::get($model->file), $thresholds->toArray()));
        $extractor->handle($model->id);
    }
}
