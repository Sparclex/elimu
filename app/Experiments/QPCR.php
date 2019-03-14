<?php

namespace App\Experiments;

use App\Exceptions\ExperimentException;
use App\Models\Result;
use App\Nova\Sample;
use App\Support\Position;
use App\Support\QPCRResultSpecifier;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Nathanmac\Utilities\Parser\Facades\Parser;
use ZipArchive;

class QPCR extends ExperimentType
{
    public const ERROR_REPLICAS = 1;
    public const ERROR_STDDEV = 2;
    public const ERROR_REPEAT = 3;

    public const POSITIVE_CONTROL = 'pos';

    public const NEGATIVE_CONTROL = 'neg';

    public const NTC_CONTROL = 'ntc';

    public const CONTROL_IDS = [self::POSITIVE_CONTROL, self::NTC_CONTROL, self::NEGATIVE_CONTROL];

    private $fileContents;

    private $data;

    private $parsedData;

    private $ignoredSamples = [];

    /**
     * Extract the sample ids of the result file
     *
     * @return Collection
     */
    public function extractSamplesIds(): Collection
    {
        return collect($this->getData()['sample'])
            ->pluck('@id')
            ->map(function ($value) {
                return strtolower($value);
            })
            ->reject(function ($id) {
                return in_array($id, self::CONTROL_IDS) ||
                    in_array($id, $this->ignoredSamples);
            })
            ->values();
    }

    protected function getData()
    {
        if (!$this->data) {
            $this->data = Parser::xml($this->getFileContents());
        }

        return $this->data;
    }

    protected function getFileContents()
    {
        if ($this->fileContents === null) {
            if (pathinfo($this->resultFile, PATHINFO_EXTENSION) == 'xml') {
                $this->fileContents = file_get_contents($this->resultFile);
            } else {
                $zip = new ZipArchive();
                if ($zip->open($this->resultFile) !== true) {
                    $this->fileContents = "";
                } else {
                    if ($zip->numFiles > 0) {
                        $this->fileContents = $zip->getFromIndex(0);
                    } else {
                        $this->fileContents = "";
                    }
                    $zip->close();
                }
            }
        }

        return $this->fileContents;
    }

    /**
     * Validates the result file
     *
     * @throws ExperimentException
     */
    public function validate(): void
    {
        $this->assertFileNotEmpty();
        $this->assertTargetsMatchParameter();
        $this->assertControlsAreValid();
    }

    protected function assertFileNotEmpty()
    {
        if (strlen($this->getFileContents()) === 0) {
            throw new ExperimentException('Invalid .rdml file');
        }
    }

    /**
     * @throws ExperimentException
     */
    protected function assertTargetsMatchParameter()
    {
        $differences = collect($this->getData()['target'])
            ->pluck('@id')
            ->map(function ($value) {
                return strtolower($value);
            })
            ->diff($this->parameters->keys());
        if ($differences->count() > 0) {
            throw new ExperimentException(
                sprintf(
                    'Targets of input parameters and .rdml files do not match: %s',
                    $differences->implode(', ')
                )
            );
        }
    }

    /**
     * @throws ExperimentException
     */
    protected function assertControlsAreValid()
    {
        foreach ($this->getControlData() as $control) {
            switch (strtolower($control['sampleId'])) {
                case self::POSITIVE_CONTROL:
                    $this->assertControlIsValid(
                        $control,
                        $this->parameters[$control['target']]['posctrl'],
                        $this->parameters[$control['target']]['cutoff']
                    );
                    break;
                case self::NEGATIVE_CONTROL:
                    $this->assertControlIsValid(
                        $control,
                        $this->parameters[$control['target']]['negctrl'],
                        $this->parameters[$control['target']]['cutoff']
                    );
                    break;
                case self::NTC_CONTROL:
                    $this->assertControlIsValid(
                        $control['cq'],
                        $this->parameters[$control['target']]['ntc'],
                        $this->parameters[$control['target']]['cutoff']
                    );
                    break;
            }
        }
    }

    protected function getControlData()
    {
        $controls = $this->getParsedData()->filter(function ($sample) {
            return in_array($sample['sampleId'], self::CONTROL_IDS);
        });

        $this->assertAllControlsExist($controls);

        return $controls;
    }

    protected function getParsedData()
    {
        if (!$this->parsedData) {
            $parsedData = [];
            foreach ($this->getData()['experiment']['run'] as $run) {
                $format = $run['pcrFormat'];
                foreach ($run['react'] as $react) {
                    if (in_array($react['sample']['@id'], $this->ignoredSamples)) {
                        continue;
                    }
                    $parsedData[] = [
                        'sampleId' => strtolower($react['sample']['@id']),
                        'target' => strtolower($react['data']['tar']['@id']),
                        'position' => Position::fromPosition($react['@id'])
                            ->withRows($format['rows'])
                            ->withColumns($format['columns'])
                            ->toLabel(),
                        'reactId' => $react['@id'],
                        'content' => collect($this->getData()['sample'])
                            ->firstWhere('@id', $react['sample']['@id'])['type'],
                        'cq' => $this->getCq($react['data'], $react['data']['tar']['@id']),
                        'data' => $react['data']['adp'] ?? []
                    ];
                }
            }
            $this->parsedData = collect($parsedData);
        }

        return $this->parsedData;
    }

    protected function getCq($data, $target)
    {
        return strtolower($data['cq']) == "nan" ? null : $data['cq'];
    }

    /**
     * @param Collection $controls
     * @throws ExperimentException
     */
    protected function assertAllControlsExist($controls): void
    {
        $missingControls = collect(self::CONTROL_IDS)
            ->reject(function ($controlId) {
                $column = $controlId == self::NTC_CONTROL ? self::NTC_CONTROL : $controlId . 'ctrl';
                return $this->parameters->pluck($column)->filter()->isEmpty();
            })
            ->diff($controls->pluck('sampleId'));

        if ($missingControls->isNotEmpty()) {
            throw new ExperimentException(
                sprintf(
                    'The following controls are missing: %s',
                    $missingControls->implode(', ')
                )
            );
        }
    }

    protected function assertControlIsValid($control, $parameter, $cutoff)
    {
        if (!$parameter) {
            return;
        }

        $cqValue = $control['cq'];

        switch (strtolower($parameter)) {
            case 'null':
                $valid = $cqValue == null;
                break;

            case 'cutoff':
                $valid = $cqValue && $cqValue <= $cutoff;
                break;

            default:
                $valid = $cqValue <= $parameter;
        }

        if (!$valid) {
            throw new ExperimentException(
                sprintf(
                    'Control %s (%s, %s) invalid',
                    $control['sampleId'],
                    $control['target'],
                    $control['position']
                )
            );
        }
    }

    /**
     * Stores the experiment results
     * @param $experiment
     *
     * @return Collection
     */
    public function getDatabaseData($experiment): Collection
    {
        return $this->getResultData()->map(function ($data) use ($experiment) {
            return [
                'sample' => $data['sampleId'],
                'target' => $data['target'],
                'primary_value' => floatval($data['cq']),
                'secondary_value' => $data['reactId'],
                'extra' => json_encode(array_except($data, 'cq', 'reactId'))
            ];
        });
    }

    /**
     * @return Collection
     */
    public function getResultData()
    {
        return $this->getParsedData()->reject(function ($sample) {
            return in_array($sample['sampleId'], self::CONTROL_IDS);
        });
    }

    public function ignore(array $ignoredSamples)
    {
        $this->ignoredSamples = $ignoredSamples;
    }

    public function results($request, $assay)
    {
        $paginator = $this->resultQuery($request, $assay)->simplePaginate($request->get('perPage', 25));

        return [
            'label' => 'Results',
            'resources' => $paginator->getCollection()
                ->mapInto(QPCRResult::class)
                ->map
                ->serializeForIndex($request),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'softDeletes' => false,
        ];
    }

    public function resultQuery($request, $assay)
    {
        $filters = [
            'assay_id' => $assay->id,
            'target' => $request ? $request->get('target') : null,
            'status' => $request ? $request->get('status') : null,
            'search' => $request ? $request->get('search') : null,
        ];
        $query = null;

        foreach ($this->parameters as $target => $parameter) {
            if ($query) {
                $query->union(self::targetQuery($parameter, $filters));
            } else {
                $query = self::targetQuery($parameter, $filters);
            }
        }

        return $query;
    }

    protected static function targetQuery($parameters, $filters)
    {
        $query = Result::where('results.target', $parameters['target'])
            ->select("results.*")
            ->selectRaw('avg(primary_value) as avg_cq')
            ->selectRaw('count(*) as replicas')
            ->selectRaw('stddev(primary_value) as stddev')
            ->selectRaw('count(case when primary_value <= ' . $parameters['cutoff'] . ' 
            and primary_value <> 0 then 1 end) as positives')
            ->join('result_data', 'results.id', 'result_id')
            ->groupBy('result_id')
            ->where('included', true);

        if (isset($filters['target'])) {
            $query->where('target', $filters['target']);
        }

        if (isset($filters['status'])) {
            switch ($filters['status']) {
                case 'valid':
                    $query->havingRaw(
                        '((positives = replicas and stddev <= ?) or (positives = 0))',
                        [$parameters['cuttoffstdev']]
                    )
                        ->having('replicas', $parameters['minvalues']);
                    break;
                case 'errors':
                    $query->havingRaw('positives <> replicas')
                        ->having('positives', '>', 0)
                        ->orHaving('replicas', '<', $parameters['minvalues'])
                        ->orHaving('stddev', '>', $parameters['cuttoffstdev']);
                    break;
                case 'positive':
                    $query->havingRaw('positives = replicas')
                        ->having('replicas', '>=', $parameters['minvalues'])
                        ->having('stddev', '<=', $parameters['cuttoffstdev']);
                    break;
                case 'negative':
                    $query->having('positives', 0)
                        ->having('replicas', '>=', $parameters['minvalues'])
                        ->havingRaw('(stddev <= ? or stddev is Null)', [$parameters['cuttoffstdev']]);
                    break;
                case 'stdev':
                    $query->havingRaw('(positives = replicas or positives = 0)')
                        ->having('replicas', '>=', $parameters['minvalues'])
                        ->having('stddev', '>', $parameters['cuttoffstdev']);
                    break;
                case 'replicates':
                    $query->having('replicas', '<', $parameters['minvalues']);
                    break;
                case 'repetition':
                    $query->havingRaw('positives <> replicas')
                        ->having('positives', '>', 0)
                        ->having('replicas', '>=', $parameters['minvalues']);
                    break;
            }
        }

        if ($filters['search']) {
            $query->whereHas('sample', function ($query) use ($filters) {
                return $query->where('sample_id', 'LIKE', sprintf('%%%s%%', $filters['search']));
            });
        }

        return $query;
    }

    public function serialize($result)
    {
        return [
            ID::make(),
            BelongsTo::make('Sample', 'sample', Sample::class),
            Text::make('Target'),
            Text::make('Result', function () use ($result) {
                $parameters = $this->parameters[$result->target];
                $error = null;
                if ($result->replicas < $parameters['minvalues']) {
                    $error = 'Not enough values';
                } elseif ($result->positives != $result->replicas && $result->positives != 0) {
                    $error = 'Needs repetition';
                } elseif ($result->positives > 0 && $result->stddev > $parameters['cuttoffstdev']) {
                    $error = 'Standard deviation too high';
                }

                if ($error) {
                    return $error;
                }

                return $result->avg_cq != null && $result->avg_cq <= $parameters['cutoff'] ? 'Positive' : 'Negative';
            })
        ];
    }

    public function export($assay)
    {
        $results = $this->resultQuery(null, $assay)->with('sample', 'sample.sampleTypes')->get();

        $sampleTypeId = $assay->definitionFile->sample_type_id;

        $table = [];

        foreach ($results->groupBy('sample_id') as $rowData) {
            $extra = optional($rowData[0]->sample->sampleTypes->firstWhere('id', $sampleTypeId))->pivot->extra;

            $row = [
                'id' => $rowData[0]->sample->sample_id,
                'subject_id' => $rowData[0]->sample->subject_id,
                'collected_at' => $rowData[0]->sample->collected_at,
                'visit_id' => $rowData[0]->sample->visit_id,
                'birthdate' => $rowData[0]->sample->birthdate,
                'gender' => $rowData[0]->sample->gender,
            ];

            foreach ($extra as $key => $value) {
                $row[$key] = $value;
            }

            foreach ($rowData as $result) {
                $specifier = new QPCRResultSpecifier(
                    $assay->definitionFile->parameters->firstWhere('target', $result->target),
                    $result
                );
                $row['replicas_' . $result->target] = $result->replicas;
                $row['mean_cq_' . $result->target] = $result->avg_cq;
                $row['sd_cq_' . $result->target] = $result->stddev;
                $row['qual_' . $result->target] = $specifier->qualitative();
                $row['quant_' . $result->target] = $specifier->quantitative();
            }

            $table[] = $row;
        }

        return $table;
    }

    public function headers($assay): array
    {
        $headings = [];

        $sampleType = $assay->results()
            ->first()
            ->sample
            ->sampleTypes()
            ->wherePivot('sample_type_id', $assay->definitionFile->sample_type_id)
            ->first();

        foreach ($sampleType->pivot->extra as $key => $value) {
            $headings[] = $key;
        }

        foreach ($assay->definitionFile->parameters->pluck('target') as $target) {
            $headings[] = 'replicas_' . $target;
            $headings[] = 'mean_cq_' . $target;
            $headings[] = 'sd_cq_' . $target;
            $headings[] = 'qual_' . $target;
            $headings[] = 'quant_' . $target;
        }


        return $headings;
    }
}
