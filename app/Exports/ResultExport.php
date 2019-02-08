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

        $headings[] = 'id';
        $headings[] = 'subject_id';
        $headings[] = 'collected_at';
        $headings[] = 'visit_id';
        $headings[] = 'birthdate';
        $headings[] = 'gender';
        $headings[] = 'extra';

        foreach ($this->assay->results->pluck('target')->unique() as $target) {
            $headings[] = 'replicas_' . $target;
            $headings[] = 'mean_cq_' . $target;
            $headings[] = 'sd_cq_' . $target;
            $headings[] = 'qual_' . $target;
            $headings[] = 'quant_' . $target;
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
                'id' => $targets[0]['sample']['sample_information']['sample_id'],
                'subject_id' => $targets[0]['sample']['sample_information']['subject_id'],
                'collected_at' => $targets[0]['sample']['sample_information']['collected_at'],
                'visit_id' => $targets[0]['sample']['sample_information']['visit_id'],
                'birthdate' => $targets[0]['sample']['sample_information']['birthdate'],
                'gender' => $targets[0]['sample']['sample_information']['gender'],
                'extra' => reset($targets[0]['sample']['extra']),
            ];

            foreach ($targets as $result) {
                $inputParameters = collect($this->assay->inputParameter->parameters)
                ->firstWhere('target', $result['target']);

                $resultData = (new ResultDataCollection($result['result_data']))->onlyAccepted();
                $output = $resultData->determineResult($inputParameters['cutoff']);

                $row['replicas_' . $result['target']] = $resultData->count();
                $row['mean_cq_' . $result['target']] = $resultData->averageCq();
                $row['sd_cq_' . $result['target']] = $resultData->cqStandardDeviation();
                $row['qual_' . $result['target']] = $this->toQualitativeWord($output);
                $row['quant_' . $result['target']] = $this->toQuantitativeValue(
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
