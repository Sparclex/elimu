<?php

namespace App\Exports;

use App\Collections\ResultDataCollection;
use App\Experiments\ExperimentType;
use App\Models\Assay;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class ResultExport extends DownloadExcel implements WithHeadings, ShouldAutoSize, FromArray
{
    /**
     * @param array|mixed $headings
     * @param array       $only
     *
     * @return $this
     */
    public function withHeadings($headings = null)
    {
        $headings = [
            'id',
            'subject_id',
            'collected_at',
            'visit_id',
            'birthdate',
            'gender',
        ];
        $headings = \is_array($headings) ? $headings : \func_get_args();

        if (0 === count($headings)) {
            $this->headingCallback = $this->autoHeading();
        } else {
            $this->headingCallback = function () use ($headings) {
                return $headings;
            };
        }

        return $this;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->experimentType->export($this->assay);
    }
}
