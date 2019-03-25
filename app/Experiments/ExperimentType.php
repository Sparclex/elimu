<?php

namespace App\Experiments;

use App\Exceptions\ExperimentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class ExperimentType
{
    protected $resultFile;

    protected $parameters;

    public function __construct($resultFile, $parameters)
    {

        $this->resultFile = $resultFile;
        if ($this->resultFile !== null && ! file_exists($this->resultFile)) {
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
     *
     * @param $experiment
     *
     * @return Collection
     */
    abstract public function getDatabaseData($experiment): Collection;

    abstract public function export($assay);

    abstract public function headers($assay): array;

    public static function indexQuery($query, $assay)
    {
        return $query;
    }

    public static function filters($request): array
    {
        return [];
    }

    public static function fields($request, $resource)
    {
        return [];
    }
}
