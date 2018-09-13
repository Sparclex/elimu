<?php

namespace App\Observers;

use App\Models\Data;
use App\ResultHandlers\Rdml\Extractor;
use App\ResultHandlers\Rdml\Processor;
use Illuminate\Support\Facades\Storage;

class ExtractSampleData
{
    public function __construct(Data $model)
    {
        $parameters = $model->experiment->assay->inputParameters()->first();
        $thresholds = $parameters->parameters->mapWithKeys(function($row) {
            return [
                $row['target'] => $row['threshold']
            ];
        });
        dump($thresholds);
        $extractor = new Extractor(new Processor(Storage::get($model->file), $thresholds->toArray()));
        $extractor->handle($model->id);
    }
}
