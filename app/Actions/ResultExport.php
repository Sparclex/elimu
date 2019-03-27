<?php

namespace App\Actions;

use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Http\Requests\ActionRequest;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Maatwebsite\LaravelNovaExcel\Requests\ExportActionRequestFactory;

class ResultExport extends DownloadExcel implements ShouldAutoSize
{
    protected $resultTypeClass;

    protected $assay;

    protected $extraKeys;

    public function handleRequest(ActionRequest $request)
    {
        $this->handleWriterType($request);
        $this->handleFilename($request);

        $this->resource = $request->resource();
        $this->request = ExportActionRequestFactory::make($request);

        $resultIds = explode(',', $this->request->resources);

        if (! $this->onlyOneAssay($resultIds)) {
            return Action::danger('Choose results from the same assay');
        }

        $result = Result::findOrFail($resultIds[0]);
        $this->assay = $result->assay;

        $this->resultTypeClass = $this->assay
            ->definitionFile
            ->resultTypeClass();

        $query = $this->resultTypeClass::exportQuery($this->assay, $resultIds);
        $extra = $result
                ->sample
                ->sampleTypes()
                ->wherePivot('sample_type_id', $this->assay->definitionFile->sample_type_id)
                ->first()
                ->pivot->extra ?? collect();

        $this->extraKeys = $extra->keys();



        $this->headings = array_merge(
            [
                'id',
                'subject_id',
                'collected_at',
                'visit_id',
                'birthdate',
                'gender',
            ],
            $extra->keys()->toArray(),
            $this->resultTypeClass::headings($result->assay)
        );

        return $this->handle($request, $this->withQuery($query));
    }

    public function map($row): array
    {
        $extra = [];
        $extraData = $row->sampleTypes->first()->pivot;

        foreach ($this->extraKeys as $key) {
            $extra[$key] = $extraData->extra[$key] ?? '';
        }

        return array_merge(
            [
                $row->sample_id,
                $row->subject_id,
                $row->collected_at,
                $row->visit_id,
                $row->birthdate,
                $row->gender,
            ],
            $extra,
            $this->resultTypeClass::exportMap($row, $this->assay)
        );
    }

    public function onlyOneAssay($resultIds)
    {
        return DB::table('results')
                ->whereIn('id', $resultIds)
                ->selectRaw('count(distinct assay_id) as count')
                ->first()->count === 1;
    }
}
