<?php

namespace App\FileTypes;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Nathanmac\Utilities\Parser\Facades\Parser;
use Symfony\Component\HttpFoundation\File\File;

class RDML
{
    public const TMP_ZIP_EXTRACT_PATH = 'tmp-rdml';

    public const CONTROL_IDS = ['pos', 'ntc', 'neg'];

    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     */
    private $file;

    /**
     * Parsed xml data
     *
     * @var array
     */
    private $data;

    /**
     * @var Collection
     */
    private $inputParameters;

    /**
     * @var Collection
     */
    private $cyclesOfQuantification;

    /**
     *
     * @var array
     */
    private $alphabet;

    private $lastError;

    /**
     * Represents a rdml file
     *
     * @param File $file
     * @param null $inputParameters
     */
    private function __construct(File $file, $inputParameters = null)
    {

        $this->file = $file;
        $this->inputParameters = $inputParameters;
        $this->alphabet = range('a', 'z');
        $this->cyclesOfQuantification = collect();
    }

    public function withInputParameters($inputParameters)
    {
        $this->inputParameters = $inputParameters;
        return $this;
    }

    public static function make(File $file, $validate = true)
    {
        $rdml = new self($file);
        if (!$validate) {
            return $rdml;
        }

        return $rdml->isValid() ? $rdml : null;
    }

    public function isValid()
    {
        return $this->containsOnlyOneFile() && $this->hasValidContent();
    }

    public function containsOnlyOneFile()
    {
        $zip = new \ZipArchive();
        if ($zip->open($this->file->getRealPath()) !== true) {
            return false;
        }
        $numberOfFiles = $zip->numFiles;

        $zip->close();

        return $numberOfFiles === 1;
    }

    public function hasValidContent()
    {
        return $this->validateKeys() && $this->validateValues();
    }

    public function validateKeys()
    {
        $requiredKeys = [
            'dateMade',
            'dateUpdated',
            'experimenter',
            'dye',
            'sample',
            'target',
            'experiment',
        ];
        $availableKeys = array_keys($this->getData());
        foreach ($requiredKeys as $key) {
            if (!in_array($key, $availableKeys)) {
                return false;
            }
        }

        return true;
    }

    public function getData()
    {
        if (!$this->data) {
            $zip = new \ZipArchive();
            if ($zip->open($this->file->getRealPath()) !== true) {
                return [];
            }
            if ($zip->numFiles !== 1) {
                $zip->close();
                return [];
            }
            $zip->extractTo(storage_path('app/' . self::TMP_ZIP_EXTRACT_PATH));
            $zip->close();

            $xmlPath = Storage::files(self::TMP_ZIP_EXTRACT_PATH)[0];
            try {
                $this->data = Parser::xml(Storage::get($xmlPath));
            } catch (\Exception $e) {
                return [];
            } finally {
                Storage::deleteDirectory(self::TMP_ZIP_EXTRACT_PATH);
            }
        }
        return $this->data;
    }

    public function validateValues()
    {
        return self::atLeastOneSampleExists() && self::allControlsExist();
    }

    public function atLeastOneSampleExists()
    {
        return count(
            array_filter(
                $this->getData()['sample'],
                function ($sample) {
                        return !in_array(strtolower($sample['@id']), self::CONTROL_IDS);
                }
            )
        ) > 0;
    }

    public function allControlsExist()
    {
        return count(
            array_filter(
                $this->getData()['sample'],
                function ($sample) {
                        return in_array(strtolower($sample['@id']), self::CONTROL_IDS);
                }
            )
        ) === count(self::CONTROL_IDS);
    }

    /**
     * @return Collection
     */
    public function getSamplesWithoutControl()
    {
        return collect(array_filter(
            $this->getData()['sample'],
            function ($sample) {
                return !in_array(strtolower($sample['@id']), self::CONTROL_IDS);
            }
        ));
    }

    /**
     * @return Collection
     */
    public function getSampleIds()
    {
        return $this->getSamplesWithoutControl()->pluck('@id');
    }


    /**
     * Returns the Cq values for all runs
     *
     * @return \Illuminate\Support\Collection
     */
    public function cyclesOfQuantification()
    {
        if ($this->cyclesOfQuantification->isEmpty()) {
            $this->computeCyclesOfQuantification();
        }

        return $this->cyclesOfQuantification;
    }

    /**
     * Returns the Cq values for all runs
     *
     * @return \Illuminate\Support\Collection
     */
    public function cyclesOfQuantificationWithoutControl()
    {
        return $this->cyclesOfQuantification()->reject(
            function ($sample) {
                return in_array(strtolower($sample['sample']), RDML::CONTROL_IDS);
            }
        );
    }

    /**
     * Computes the Cq values for all runs
     *
     * @return \Illuminate\Support\Collection
     */
    public function computeCyclesOfQuantification()
    {
        $samples = $this->getSamples();
        $dyes = $this->getDyes();
        $targets = $this->getTargets();
        foreach ($this->getExperimentRuns() as $run) {
            $format = $run['pcrFormat'];
            foreach ($run['react'] as $react) {
                $target = $targets[$react['data']['tar']['@id']];
                $sample = $samples[$react['sample']['@id']];
                $this->cyclesOfQuantification->push(
                    [
                        'well' => $this->determineWell(
                            $format['columns'],
                            $format['rowLabel'],
                            $format['columnLabel'],
                            $react['@id']
                        ),
                        'reactId' => $react['@id'],
                        'fluor' => $dyes[$target['dyeId']['@id']]['@id'],
                        'target' => $target['@id'],
                        'content' => $sample['type'],
                        'sample' => $sample['@id'],
                        'cq' => $this->computeCq($react['data']['adp'], $this->getThresholds()[$target['@id']]),
                    ]
                );
            }
        }

        return $this->cyclesOfQuantification = $this->cyclesOfQuantification->sortBy('well')->sortByDesc('target');
    }

    /**
     * @return Collection
     */
    public function getSamples()
    {
        return collect($this->getData()['sample'])->keyBy('@id');
    }

    public function getDyes()
    {
        return collect($this->getData()['dye'])->keyBy('@id');
    }

    public function getTargets()
    {
        return collect($this->getData()['target'])->keyBy('@id');
    }

    public function getExperimentRuns()
    {
        return collect($this->getData()['experiment']['run']);
    }

    private function determineWell($numberOfColumns, $rowLabel, $columnLabel, $id)
    {
        $id = (int)$id;
        $column = $id % $numberOfColumns;
        $row = (($id - $column) / $numberOfColumns) + 1;

        return $this->convertNumberToLabel($rowLabel, $row) . $this->convertNumberToLabel($columnLabel, $column);
    }

    private function convertNumberToLabel($label, $number)
    {
        if ($label === 'ABC') {
            return strtoupper($this->alphabet[$number - 1]);
        }
        if ($label === 'abc') {
            return $this->alphabet[$number - 1];
        }

        return sprintf('%02d', $number);
    }

    private function computeCq($adp, $threshold)
    {
        $lastPoint = null;
        foreach ($adp as $point) {
            if ($point['fluor'] >= $threshold) {
                return $this->determineIntersect($lastPoint, $point, $threshold);
            }
            $lastPoint = $point;
        }

        return null;
    }

    private function determineIntersect($pointA, $pointB, $y)
    {
        // m = (y2 - y1) / (x2 - x1)
        $slope = ($pointB['fluor'] - $pointA['fluor']) / ($pointB['cyc'] - $pointA['cyc']);
        // b = (y1 - mx1)
        $b = $pointB['fluor'] - $slope * $pointB['cyc'];

        // x = (y - b) / m
        return ($y - $b) / $slope;
    }

    public function getExperimentData()
    {
        $data = [];
        foreach ($this->getExperimentRuns() as $run) {
            foreach ($run['react'] as $react) {
                if (!isset($data[$react['sample']['@id']])) {
                    $data[$react['sample']['@id']] = [];
                }
                $data[$react['sample']['@id']][$react['data']['tar']['@id']] = $react['data']['adp'];
            }
        }

        return $data;
    }

    public function getChartData()
    {
        $data = [];
        foreach ($this->getExperimentRuns() as $run) {
            foreach ($run['react'] as $react) {
                if (!isset($data[$react['sample']['@id']])) {
                    $data[$react['sample']['@id']] = [];
                }
                $data[$react['sample']['@id']][] = [
                    'label' => $react['data']['tar']['@id'],
                    'data' => collect($react['data']['adp'])->map(
                        function ($item) {
                            return $item['fluor'];
                        }
                    ),
                    'borderColor' => 'red',
                    'backgroundColor' => 'red',
                    'lineTension' => 0.3,
                ];
            }
        }

        return $data;
    }

    public function getChartDataFor($sampleId, $position, $target)
    {
        foreach ($this->getExperimentRuns() as $run) {
            $format = $run['pcrFormat'];
            $reactId = $this->determinePosition(
                $format['columns'],
                $format['rowLabel'],
                $format['columnLabel'],
                $position
            );
            foreach ($run['react'] as $react) {
                if ($react['@id'] == $reactId &&
                    $react['data']['tar']['@id'] == $target && $react['sample']['@id'] == $sampleId) {
                    return [
                        [
                            'label' => $target,
                            'data' => collect($react['data']['adp'])->map(
                                function ($item, $key) {
                                    return [
                                        'x' => $key + 1,
                                        'y' => $item['fluor']
                                    ];
                                }
                            ),
                            'borderColor' => '#4099de',
                            'backgroundColor' => '#4099de',
                            'fill' => false,
                            'lineTension' => 0.3,
                        ],
                        [
                            'label' => 'Threshold',
                            'data' => array_fill(0, count($react['data']['adp']), $this->getThresholds()[$target]),
                            'borderColor' => '#b3b9bf',
                            'backgroundColor' => '#b3b9bf',
                            'fill' => false,
                            'lineTension' => 0.3,
                        ]
                    ];
                }
            }
        }

        return [];
    }

    public function getThresholds()
    {
        return $this->inputParameters->pluck('threshold', 'target');
    }

    public function determinePosition($numberOfColumns, $rowLabel, $columnLabel, $well)
    {
        $offset = 1;
        if (strtolower($rowLabel) == 'abc') {
            $rowDigit = array_search(strtolower(substr($well, 0, 1)), $this->alphabet);
        } else {
            $rowDigit = (int)substr($well, 0, 2);
            $offset = 2;
        }

        if (strtolower($columnLabel) == 'abc') {
            $columnDigit = substr($well, $offset, 1);
        } else {
            $columnDigit = (int)substr($well, $offset, 2);
        }
        return ($rowDigit) * $numberOfColumns + $columnDigit;
    }

    public function getSampleTargets()
    {
        $data = [];
        $controlSamples = $this->getControlSamples();
        foreach ($this->getExperimentRuns() as $run) {
            foreach ($run['react'] as $react) {
                if ($controlSamples->contains($react['sample']['@id'])) {
                    continue;
                }
                $data[] = [
                    'sample' => $react['sample']['@id'],
                    'target' => $react['data']['tar']['@id'],
                    'react' => $react['@id'],
                ];
            }
        }

        return collect($data);
    }

    public function hasValidTargets()
    {
        if (!$this->inputParameters) {
            return false;
        }
        $inputTargets = $this->inputParameters->pluck('target');
        $overfulTargets = collect();
        foreach ($this->getTargets()->keys() as $target) {
            $index = $inputTargets->search($target);
            if ($index === false) {
                $overfulTargets->push($target);
            } else {
                $inputTargets->splice($index, 1);
            }
        }
        if ($overfulTargets->isNotEmpty() && $inputTargets->isNotEmpty()) {
            $this->lastError = sprintf(
                'Targets "%s" are present in rdml file but not in the input parameters.
            Targets "%s" are present in the input parameters but not in the rdml file',
                $overfulTargets->implode(', '),
                $inputTargets->implode(', ')
            );
        } else {
            if ($overfulTargets->isNotEmpty()) {
                $this->lastError = sprintf(
                    'Targets "%s" are present in rdml file but not in the input parameters.',
                    $overfulTargets->implode(', ')
                );
            } else {
                if ($inputTargets->isNotEmpty()) {
                    $this->lastError = sprintf(
                        'Targets "%s" are present in the input parameters but not in the rdml file',
                        $inputTargets->implode(', ')
                    );
                }
            }
        }
        if ($this->lastError) {
            return false;
        }
        return true;
    }

    public function hasEnoughRepetitions()
    {
        return true;
    }

    public function hasCorrectDeviation()
    {
        return true;
    }

    public function getLastError()
    {
        return $this->lastError;
    }
}
