<?php

namespace App\Exceptions;

class AssayNotFound extends AnalyzingResultsException
{
    public function __construct(string $assayName)
    {
        parent::__construct("Assay with name '{$assayName}' not found.");
    }
}
