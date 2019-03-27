<?php

namespace App\Experiments;

use App\Exceptions\ExperimentException;
use App\Models\Sample;
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

    public static function exportQuery($assay, $resultIds)
    {
        return Sample::whereHas(
            'results',
            function ($query) use ($resultIds) {
                return $query->whereIn('results.id', $resultIds);
            }
        )->with(
            [
                'results' => function ($query) use ($resultIds) {
                    return $query->whereIn('id', $resultIds);
                },
                'results.resultData',
                'sampleTypes' => function ($query) use ($assay) {
                    return $query->where('sample_types.id', $assay->definitionFile->sample_type_id);
                },
                ]
        );
    }

    public static function headings($assay)
    {
        return [];
    }

    public static function exportMap($row, $assay)
    {
        return [];
    }

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
