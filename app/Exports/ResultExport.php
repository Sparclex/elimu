<?php

namespace App\Exports;

use App\Models\Assay;
use App\Collections\ResultDataCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ResultExport implements FromArray, WithHeadings, ShouldAutoSize
{
    private $assay;

    public function __construct(Assay $assay)
    {
        $this->assay = $assay;
    }

    public function headings() : array
    {

        $headings[] = 'Sample ID';

        foreach ($this->assay->results->pluck('target')->unique() as $target) {
            $headings[] = 'n replicates accepted for target ' . $target;
            $headings[] = 'mean Cq target ' . $target;
            $headings[] = 'sd Cq target ' . $target;
            $headings[] = 'qualitative target ' . $target;
            $headings[] = 'quantitative target ' . $target;
        }

        return $headings;
    }

    /**
    * @return array
    */
    public function array() : array
    {
        $this->assay->load('results.sample.sampleInformation', 'results.resultData', 'inputParameter');

        $data = [];

        foreach (collect($this->assay->results->toArray())->groupBy('sample_id') as $targets) {
            $row = [
                'Sample ID' => $targets[0]['sample']['sample_information']['sample_id'],
            ];

            foreach ($targets as $result) {
                $inputParameters = collect($this->assay->inputParameter->parameters)
                ->firstWhere('target', $result['target']);

                $resultData = (new ResultDataCollection($result['result_data']))->onlyAccepted();
                $output = $resultData->determineResult($inputParameters['cutoff']);

                $row['n replicates accepted for target ' . $result['target']] = $resultData->count();
                $row['mean Cq target ' . $result['target']] = $resultData->averageCq();
                $row['sd Cq target ' . $result['target']] = $resultData->cqStandardDeviation();
                $row['qualitative target ' . $result['target']] = $this->toQualitativeWord($output);
                $row['quantitative target ' . $result['target']] = $this->toQuantitativeValue(
                    $output,
                    $resultData,
                    $inputParameters['slope'],
                    $inputParameters['intercept'],
                    strtolower($inputParameters['quant']) == 'yes'
                );
            }

            $data[] = $row;
        }

        return array_values($data);
    }


    private function toQualitativeWord($output)
    {
        switch ($output) {
            case 1:
                return 'Positive';
            case 0:
                return 'Negative';
            default:
                return 'Needs Repetition';
        }
    }

    private function toQuantitativeValue($value, $data, $slope, $intercept, $shouldQuantify)
    {
        if (!$shouldQuantify || $value !== 1) {
            return "";
        }
        return $data->quantitativeOutput($slope, $intercept);
    }
}
