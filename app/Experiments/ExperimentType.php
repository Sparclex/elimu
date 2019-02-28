<?php

namespace App\Experiments;

use App\Exceptions\ExperimentException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

abstract class ExperimentType
{
    protected $resultFile;
    protected $parameters;

    public function __construct($resultFile, $parameters)
    {

        $this->resultFile = $resultFile;
        if ($this->resultFile !== null && !file_exists($this->resultFile)) {
            throw new InvalidArgumentException('Result file not found');
        }
        $this->parameters = $parameters;
    }

    /**
     * Extract the sample ids of the result file
     *
     * @return Collection
     */
    abstract public function extractSamplesIds(): Collection;

    /**
     * Validates the result file
     *
     * @throws ExperimentException
     */
    abstract public function validate(): void;

    /**
     * Stores the experiment results
     * @param $experiment
     *
     * @return Collection
     */
    abstract public function getDatabaseData($experiment): Collection;

    abstract public function results($filters);
}
