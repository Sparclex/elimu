<?php

namespace App\ResultHandlers\Rdml;

use Nathanmac\Utilities\Parser\Facades\Parser;

/**
 * Extract relevant data from an rdml
 *
 */
class Processor implements ProcessorContract
{
    /**
     * The whole data extracted from a rdml
     *
     * @var \Illuminate\Support\Collection
     */
    protected $rdml;

    /**
     * The defined thresholds with targets as key
     *
     * @var array
     */
    protected $thresholds;

    /**
     * Small case alphabet
     *
     * @var array
     */
    private $alphabet;

    /**
     * The computed cycles of quantification
     *
     * @var \Illuminate\Support\Collection
     */
    private $cyclesOfQuantification;

    /**
     * Creates a new Rdml Manager instance
     *
     * @param string $rdmlFile
     * @param array $thresholds
     */
    public function __construct($rdmlFile, array $thresholds = [])
    {
        $this->rdml = collect(Parser::xml($rdmlFile));
        $this->thresholds = $thresholds;
        $this->alphabet = range('a', 'z');
        $this->cyclesOfQuantification = collect();
    }

    /**
     * Creates a new Rdml Manger instance
     *
     * @param string $rdmlFile
     * @return \App\Manager
     */
    public static function make($rdmlFile, array $thresholds)
    {
        return (new self($rdmlFile, $thresholds))->parse();
    }

    public function withTresholds(array $thresholds)
    {
        $this->thresholds = $thresholds;

        return $this;
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
                        'cq' => $this->computeCq($react['data']['adp'], $this->thresholds[$target['@id']]),
                    ]
                );
            }
        }

        return $this->cyclesOfQuantification = $this->cyclesOfQuantification->sortBy('well')->sortByDesc('target');
    }

    public function getSamples()
    {
        return collect($this->rdml['sample'])->keyBy('@id');
    }

    public function getDyes()
    {
        return collect($this->rdml['dye'])->keyBy('@id');
    }

    public function getTargets()
    {
        return collect($this->rdml['target'])->keyBy('@id');
    }

    public function getExperimentRuns()
    {
        return collect($this->rdml['experiment']['run']);
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

    public function getData()
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
                            'data' => array_fill(0, count($react['data']['adp']), $this->thresholds[$target]),
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
}
