<?php

namespace App\Collections;

use App\Exceptions\InputParameterTargetMissing;
use Illuminate\Support\Collection;

class RdmlCollection extends Collection
{
    public const POSITIVE_CONTROL = 'pos';

    public const NEGATIVE_CONTROL = 'neg';

    public const NTC_CONTROL = 'ntc';

    public const CONTROL_IDS = [self::POSITIVE_CONTROL, self::NTC_CONTROL, self::NEGATIVE_CONTROL];

    public const ALPHABET = [
        "a",
        "b",
        "c",
        "d",
        "e",
        "f",
        "g",
        "h",
        "i",
        "j",
        "k",
        "l",
        "m",
        "n",
        "o",
        "p",
        "q",
        "r",
        "s",
        "t",
        "u",
        "v",
        "w",
        "x",
        "y",
        "z"
    ];


    /**
     * Retrieves all available sample and controls
     *
     * @return Collection
     */
    public function samplesAndControls()
    {
        return (new parent($this->get('sample')))->keyBy('@id');
    }

    /**
     * Retrieves all available sample ids
     *
     * @return Collection
     */
    public function samples()
    {
        return (new parent($this->get('sample')))->reject(function ($sample) {
            return RdmlCollection::isControlId($sample['@id']);
        })->keyBy('@id');
    }

    /**
     * Retrieves all available sample ids
     *
     * @return Collection
     */
    public function sampleIds()
    {
        return (new parent($this->get('sample')))->pluck('@id')->values()->reject(function ($sample) {
            return RdmlCollection::isControlId($sample);
        });
    }

    /**
     * Retrieves all available control ids
     *
     * @return Collection
     */
    public function controls()
    {
        return (new parent($this->get('sample')))->pluck('@id')->values()->filter(function ($sample) {
            return RdmlCollection::isControlId($sample);
        });
    }

    /**
     * Retrieves all available dyes
     *
     * @return Collection
     */
    public function dyes()
    {
        return (new parent($this->pluck('dye.@id')))->values();
    }

    /**
     * Retrieves all available Targets
     *
     * @return Collection
     */
    public function targets()
    {
        return (new parent($this->get('target')))->keyBy('@id');
    }

    /**
     * Retrieves a list of the experiment runs
     *
     * @return Collection
     */
    public function experimentRuns()
    {
        return new parent($this->get('experiment')['run']);
    }

    /**
     * Retrieves the data grouped by sample and target
     *
     * @return Collection
     */
    public function experimentData()
    {
        $data = new static();
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

    /**
     * Prepares the chart data used for chartjs
     *
     * @param $sampleId
     * @param $position equal to the well
     * @param $target
     * @param $thresholds
     * @return array
     */
    public function getChartDataFor($sampleId, $position, $target, $thresholds)
    {
        foreach ($this->experimentRuns() as $run) {
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
                            'data' => array_fill(0, count($react['data']['adp']), $thresholds[$target]),
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

    /**
     * Opposite of @see RdmlCollection::determineWell
     *
     * @param $numberOfColumns
     * @param $rowLabel
     * @param $columnLabel
     * @param $well
     * @return bool|float|int|string
     */
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

    /**
     * Determines if the given id is part of the three defined control ids @see RdmlCollection::CONTROL_IDS
     *
     * @param $sampleId
     *
     * @return bool
     */
    public static function isControlId($sampleId)
    {
        return in_array(strtolower($sampleId), self::CONTROL_IDS);
    }


    /**
     * @param $thresholds
     * @return Collection array of array with keys: sampleId, target, position, reactId, fluor, content, cq, data
     * @throws InputParameterTargetMissing
     */
    public function parse($thresholds = null)
    {
        try {
            $items = new parent();
            $samples = (new parent($this->get('sample')))->keyBy('@id');
            $targets = (new parent($this->get('target')))->keyBy('@id');
            foreach (new parent($this->get('experiment')['run']) as $run) {
                $format = $run['pcrFormat'];
                foreach ($run['react'] as $react) {
                    $target = $targets[$react['data']['tar']['@id']];
                    $sample = $samples[$react['sample']['@id']];
                    $items->push(
                        [
                            'sampleId' => $sample['@id'],
                            'target' => $target['@id'],
                            'position' => $this->determineWell(
                                $format['columns'],
                                $format['rowLabel'],
                                $format['columnLabel'],
                                $react['@id']
                            ),
                            'reactId' => $react['@id'],
                            'fluor' => $target['dyeId']['@id'],
                            'content' => $sample['type'],
                            'cq' => $this->getCq($react['data'], $thresholds, $target['@id']),
                            'data' => $react['data']['adp'] ?? []
                        ]
                    );
                }
            }
            return $items;
        } catch (\OutOfBoundsException $e) {
            dump($e);
        }
        return new parent();
    }


    /**
     * Reconstructs the well (position) of the sample by using the react id
     *
     * @param $numberOfColumns
     * @param $rowLabel
     * @param $columnLabel
     * @param $reactId
     *
     * @return string
     */
    private function determineWell($numberOfColumns, $rowLabel, $columnLabel, $reactId)
    {
        $reactId = (int)$reactId;
        $column = $reactId % $numberOfColumns;
        $row = (($reactId - $column) / $numberOfColumns) + 1;

        return $this->convertNumberToLabel($rowLabel, $row) . $this->convertNumberToLabel($columnLabel, $column);
    }

    /**
     * Returns the alphabetic label
     *
     * @param $label
     * @param $number
     * @return string
     */
    private function convertNumberToLabel($label, $number)
    {
        if ($label === 'ABC') {
            return strtoupper(self::ALPHABET[$number - 1]);
        }
        if ($label === 'abc') {
            return self::ALPHABET[$number - 1];
        }

        return sprintf('%02d', $number);
    }

    /**
     * Retrieves the Cq value from the rdml file or calculates it by the given adp data
     *
     * @param $data
     * @param $threshold
     * @return float|int|null
     * @throws InputParameterTargetMissing
     */
    private function getCq($data, $threshold, $target)
    {
        if ($data['cq']) {
            return strtolower($data['cq']) == "nan" ? null : $data['cq'];
        }
        if (!isset($threshold[$target])) {
            throw new InputParameterTargetMissing($target);
        }
        if (isset($data['adp'])) {
            $lastPoint = null;
            foreach ($data['adp'] as $point) {
                if ($point['fluor'] >= $threshold) {
                    return $this->determineIntersect($lastPoint, $point, $threshold);
                }
                $lastPoint = $point;
            }
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
