<?php

namespace App\Observers;

use App\Models\Experiment;
use App\Support\ExperimentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResultExtractor
{
    public function __construct(Experiment $experiment)
    {
        if (!$experiment->result_file) {
            return;
        }

        $experimentType = config('lims.result_types.' . $experiment->assay->definitionFile->result_type);

        $experimentType = new $experimentType(
            storage_path('app/' .$experiment->result_file),
            $experiment->assay->definitionFile->parameters->keyBy('target')
        );

        $experimentType->ignore(
            [
                '6179592',
                '7179593',
                '8179594',
                '9179595',
                '1179596',
                '5176792',
                '5176801',
                '9176805',
            ]
        );

        $manager = new ExperimentManager($experimentType, $experiment);



        $manager->validate();

        $manager->store();
    }
}
