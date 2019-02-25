<?php

namespace App\Experiments;

use App\Exceptions\ExperimentException;
use App\Models\Result;
use App\Models\ResultData;
use App\Models\Sample;
use App\Support\Position;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

    protected function getData()
    {
        if (!$this->data) {
            $this->data = Parser::xml($this->getFileContents());
        }

        return $this->data;
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
                $valid = $cqValue > $parameter;
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

    protected function getControlData()
    {
        $controls = $this->getParsedData()->filter(function ($sample) {
            return in_array($sample['sampleId'], self::CONTROL_IDS);
        });

        $this->assertAllControlsExist($controls);

        return $controls;
    }

    /**
     * @param Collection $controls
     * @throws ExperimentException
     */
    protected function assertAllControlsExist($controls): void
    {
        $missingControls = collect(self::CONTROL_IDS)->diff($controls->pluck('sampleId'));

        if ($missingControls->isNotEmpty()) {
            throw new ExperimentException(
                sprintf(
                    'The following controls are missing: %s',
                    $missingControls->implode(', ')
                )
            );
        }
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

    public static function resultQuery($parameters)
    {
        $query = null;
        foreach ($parameters as $target => $parameter) {
            if ($query) {
                $query->union(self::targetQuery($parameter));
            } else {
                $query = self::targetQuery($parameter);
            }
        }
        return $query;
    }

    protected static function targetQuery($parameters)
    {
        return DB::table('results as '. $parameters['target'])
        ->where('target', $parameters['target'])
        ->select($parameters['target']. ".*")
        ->addSubSelect(
            'avg_cq',
            ResultData::where('included', true)
            ->whereColumn('result_id', $parameters['target'].'.id')
            ->selectRaw('avg(primary_value)')
        )
        ->addSubSelect(
            'replicas',
            ResultData::where('included', true)
                ->whereColumn('result_id', $parameters['target'].'.id')
                ->selectRaw('count(*)')
        )->addSubSelect(
            'stddev',
            ResultData::where('included', true)
                ->whereColumn('result_id', $parameters['target'].'.id')
                ->selectRaw('stddev(primary_value)')
        )->addSubSelect(
            'positives',
            ResultData::where('included', true)
                ->whereColumn('result_id', $parameters['target'].'.id')
                ->where('primary_value', '<=', $parameters['cutoff'])
                ->whereNotNull('primary_value')
                ->selectRaw('count(*)')
        );
    }
}
