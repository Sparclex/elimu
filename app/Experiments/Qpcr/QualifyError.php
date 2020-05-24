<?php

namespace App\Experiments\Qpcr;

class QualifyError implements QualifiedResponse
{
    /** @var string */
    public $errorMessage;

    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function message(): string
    {
        return $this->errorMessage;
    }
}
