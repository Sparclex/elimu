<?php

namespace App\Exceptions;

class AssayTypeNotSupported extends AnalyzingResultsException
{
    public function __construct(string $resultType)
    {
        parent::__construct("Assay type '{$resultType}' is currently not supported by this api.");
    }
}
