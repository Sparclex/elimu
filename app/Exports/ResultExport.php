<?php

namespace App\Exports;

use App\Collections\ResultDataCollection;
use App\Experiments\ExperimentType;
use App\Models\Assay;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResultExport implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable;

    /**
     * @var ExperimentType
     */
    private $experimentType;
    /**
     * @var Assay
     */
    private $assay;

    public function __construct(ExperimentType $experimentType, Assay $assay)
    {
        $this->experimentType = $experimentType;
        $this->assay = $assay;
    }

    public function headings(): array
    {
        $headings = [
            'id',
            'subject_id',
            'collected_at',
            'visit_id',
            'birthdate',
            'gender',
        ];

        return array_merge($headings, $this->experimentType->headers($this->assay));
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->experimentType->export($this->assay);
    }
}
