<?php

namespace App\Queries;

use Illuminate\Database\Query\Builder;

interface AnalizedResultsQuery
{
    public function run(int $studyId, int $assayId, array $parameters): Builder;
}
