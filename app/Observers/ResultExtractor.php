<?php

namespace App\Observers;

use App\Models\Experiment;
use App\Support\ExperimentManager;

class ResultExtractor
{
    public function __construct(Experiment $experiment)
    {
        if (!$experiment->result_file || $experiment->isClean('result_file')) {
            return;
        }

        $experimentType = config('elimu.result_types.' . $experiment->assay->definitionFile->result_type);

        $experimentType = new $experimentType(
            storage_path('app/' . $experiment->result_file),
            $experiment->assay->definitionFile->parameters->keyBy('target')
        );

        $manager = new ExperimentManager($experimentType, $experiment);


        $manager->validate();

        $manager->store();
    }
}
