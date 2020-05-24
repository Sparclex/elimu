<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AnalyzingResultsException;
use App\Models\Study;
use App\Queries\GetQpcrResults;
use App\Services\ListEvaluatedResults;

class ListAllResultsController
{
    public function __invoke($study, $assay, ListEvaluatedResults $listEvaluatedResults, GetQpcrResults $getQpcrResults)
    {
        try {
            $results = $listEvaluatedResults->get(
                Study::withoutGlobalScopes()->where('study_id', $study)->firstOrFail(),
                $assay,
                $getQpcrResults
            );

            return [
                'result' => 'success',
                'data' => $results
            ];
        } catch (AnalyzingResultsException $exception) {
            return [
                'result' => 'error',
                'data' => [
                    'message' => $exception->getMessage()
                ]
            ];
        }
    }
}
