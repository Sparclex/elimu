<?php

namespace App\Experiments;

use App\Exceptions\ExperimentException;
use App\Models\ResultData;
use Exception;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\Text;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NonQPCR extends ExperimentType
{
    protected $data;

    protected static $requiredColumns = ['sample_id', 'primary_value', 'target'];

    public function __construct($resultFile, $parameters)
    {
        parent::__construct($resultFile, $parameters);

        $this->parameters = self::convertParameters($parameters);
    }

    protected static function convertParameters($parameters)
    {
        return $parameters->map(
            function ($parameter) {
                $parameter['labels'] = collect(array_map('trim', explode(',', $parameter['labels'])))
                    ->sort()
                    ->values();

                return $parameter;
            }
        )->keyBy('target');
    }

    /**
     * Extract the sample ids of the result file
     *
     * @return Collection
     */
    public function extractSamplesIds(): Collection
    {
        return $this->getData()->pluck('sample_id')->map(
            function ($value) {
                return strtolower($value);
            }
        );
    }

    public function getData()
    {
        if (! $this->data) {
            $reader = IOFactory::createReader(Excel::XLSX);
            $reader->setReadDataOnly(true);
            $data = collect(
                $reader->load($this->resultFile)
                    ->getActiveSheet()
                    ->toArray()
            )
                ->filter(
                    function ($row) {
                        return array_filter($row);
                    }
                );

            $this->data = $data
                ->except(0)
                ->map(
                    function ($row) use ($data) {
                        $combined = array_combine($data->first(), $row);
                        ksort($combined);

                        return $combined;
                    }
                )
                ->filter(
                    function ($row) {
                        return count(array_filter($row));
                    }
                )
                ->values();
        }

        return $this->data;
    }

    /**
     * Validates the result file
     *
     * @throws ExperimentException
     */
    public function validate(): void
    {
        $this->assertExtensionCorrect();
        $this->assertFileNotEmpty();
        $this->assertRequiredColumnsExists();
        $this->assertRequiredColumnsAreFilled();
        $this->assertTargetsAreValid();
        $this->assertPrimaryValuesAreValid();
    }

    protected static function valueToLabel($targetParameters, $value)
    {
        if (! isset($targetParameters['labels'])) {
            return $value;
        }

        return collect(array_map('trim', explode(',', $targetParameters['labels'])))
            ->sort()
            ->values()
            ->get((int) $value, null);
    }

    protected static function labelToValue($targetParameters, $value)
    {
        if (! isset($targetParameters['labels'])) {
            return $value;
        }

        return $targetParameters['labels']
            ->isearch($value, null);
    }

    /**
     * Stores the experiment results
     *
     * @param $experiment
     *
     * @return Collection
     */
    public function getDatabaseData($experiment): Collection
    {
        return $this->getData()->map(
            function ($row) {
                return [
                    'sample' => $row['sample_id'],
                    'target' => $row['target'],
                    'primary_value' => self::labelToValue(
                        $this->parameters[strtolower($row['target'])],
                        $row['primary_value']
                    ),
                    'secondary_value' => $row['secondary_value'] ?? null,
                    'extra' => collect($row)
                        ->except(['sample_id', 'target', 'primary_value', 'secondary_value'])
                        ->toJson(),
                ];
            }
        );
    }

    public static function exportMap($row, $assay)
    {
        $map = [];

        foreach ($assay->definitionFile->parameters as $targetParameter) {
            $result = $row->results->first(
                function ($result) use ($targetParameter) {
                    return strtolower($result->target) == strtolower($targetParameter['target']);
                }
            );

            if (! $result) {
                $map[] = '';
                continue;
            }

            $map[] = self::valueToLabel($targetParameter, $result->resultData->first()->primary_value);
        }

        return $map;
    }

    public static function headings($assay): array
    {
        $headings = [];

        foreach ($assay->definitionFile->parameters as $targetParameter) {
            $headings[] = $targetParameter['target'];
        }

        return $headings;
    }

    protected function assertExtensionCorrect()
    {
        if (pathinfo($this->resultFile)['extension'] != 'xlsx') {
            throw new ExperimentException('Only .xlsx Files allowed');
        }
    }

    protected function assertFileNotEmpty()
    {
        if ($this->getData()->isEmpty()) {
            throw new ExperimentException('The file is empty');
        }
    }

    protected function assertRequiredColumnsExists()
    {
        foreach (self::$requiredColumns as $column) {
            if (! isset($this->getData()->first()[$column])) {
                throw new ExperimentException(sprintf('The column %s is required', $column));
            }
        }
    }

    protected function assertRequiredColumnsAreFilled()
    {
        $this->getData()->each(
            function ($row, $key) {
                foreach (self::$requiredColumns as $column) {
                    if ($row[$column] === null) {
                        throw new ExperimentException(sprintf('%s missing in row %d', $column, $key + 1));
                    }
                }
            }
        );
    }

    protected function assertPrimaryValuesAreValid()
    {
        $this->getData()->each(
            function ($row, $key) {
                $target = strtolower($row['target']);
                if ($this->parameters[$target]['labels']->isearch(trim($row['primary_value'])) === false) {
                    throw new ExperimentException(
                        sprintf(
                            'Row %d has an invalid primary value. Only the following values are allowed: %s',
                            $key + 1,
                            $this->parameters[$target]['labels']->implode(', ')
                        )
                    );
                }
            }
        );
    }

    protected function assertTargetsAreValid()
    {
        $this->getData()->each(
            function ($row, $key) {
                $target = strtolower($row['target']);
                if (! $this->parameters->has($target)) {
                    throw new ExperimentException(
                        sprintf(
                            'Target %s in row %d does not exist in the definition file',
                            $row['target'],
                            $key + 1
                        )
                    );
                }
            }
        );
    }

    public static function primaryValue($request, $parameters)
    {
        return Text::make('Primary Value')
            ->displayUsing(
                function ($value) use ($parameters) {
                    return self::valueToLabel($parameters, $value);
                }
            );
    }

    public static function indexQuery($query, $assay)
    {
        return $query->join('result_data', 'results.id', 'result_id')
            ->select('results.*')
            ->addSubSelect(
                'result',
                ResultData::whereColumn('result_id', 'results.id')
                    ->where('included', true)
                    ->select('primary_value')
                    ->limit(1)
            )
            ->addSubSelect(
                'replicas',
                ResultData::selectRaw('count(*)')
                    ->whereColumn('result_data.result_id', 'results.id')
                    ->where('included', true)
            );
    }

    public static function filters($request): array
    {
        return [];
    }

    public static function fields($request, $resource)
    {
        return [
            Text::make('Result')
                ->displayUsing(
                    (function ($value) {

                        $parameters = $this->assay
                            ->definitionFile
                            ->parameters
                            ->firstWhere('target', strtolower($this->target));

                        return self::valueToLabel($parameters, $value);
                    })->bindTo($resource)
                )->sortable(),
        ];
    }
}
