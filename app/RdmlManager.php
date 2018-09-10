<?php

namespace App;

use Nathanmac\Utilities\Parser\Facades\Parser;

/**
 * Extract relevant data from an rdml
 *
 */
class RdmlManager
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
    public function __construct($rdmlFile, array $thresholds)
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
     * @return \App\RdmlManager
     */
    public static function make($rdmlFile, array $thresholds)
    {
        return (new self($rdmlFile, $thresholds))->parse();
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
                            $format['columns'], $format['rowLabel'], $format['columnLabel'], $react['@id']),
                        'fluor' => $dyes[$target['dyeId']['@id']]['@id'],
                        'target' => $target['@id'],
                        'content' => $sample['type'],
                        'sample' => $sample['@id'],
                        'Cq' => $this->computeCq($react['data']['adp'], $this->thresholds[$target['@id']]),
                    ]);
            }
        }

        return $this->cyclesOfQuantification = $this->cyclesOfQuantification->sortBy('well')->sortByDesc('target');
    }

    private function determineWell($numberOfColumns, $rowLabel, $columnLabel, $id)
    {
        $id = (int) $id;
        $column = $id % $numberOfColumns;
        $row = (($id - $column) / $numberOfColumns) + 1;

        return $this->convertNumberToLabel($rowLabel, $row).$this->convertNumberToLabel($columnLabel, $column);
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

    public function getData()
    {
        $data = [];
        foreach ($this->getExperimentRuns() as $run) {
            foreach ($run['react'] as $react) {
                if (! isset($data[$react['sample']['@id']])) {
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
                if (! isset($data[$react['sample']['@id']])) {
                    $data[$react['sample']['@id']] = [];
                }
                $data[$react['sample']['@id']][] = [
                    'label' => $react['data']['tar']['@id'],
                    'data' => collect($react['data']['adp'])->map(
                    function ($item) {
                        return $item['fluor'];
                    }),
                    'borderColor' => 'red',
                    'backgroundColor' => 'red',
                    'lineTension' => 0.3
                ];
            }
        }

        return $data;
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
}
