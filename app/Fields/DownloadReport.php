<?php

namespace App\Fields;

use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Select;

class DownloadReport extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'download-report';

    public $showOnCreation = false;

    public $showOnUpdate = false;

    public $showOnIndex = true;
    private $sampleId;

    public function __construct($sampleId)
    {
        parent::__construct('');
        $this->withMeta(['id' => $sampleId]);
        $this->sampleId = $sampleId;
        $this->withMeta(['fields' => $this->fields()]);
    }

    public function fields()
    {
        $experimentsIds = DB::table('results')
            ->leftJoin('result_data', 'results.id', 'result_data.result_id')
            ->where('sample_id', $this->sampleId)
            ->groupBy('results.id')
            ->havingRaw('count(result_data.id) = count(result_data.status)')
            ->pluck('assay_id', 'assay_id');

        if (!$experimentsIds->count()) {
            return [];
        }
        return [
            Select::make('Assay')
                ->options($experimentsIds->toArray())
                ->rules('required', 'exists:assays,id')
                ->help('Only accepted sample data can be used to generate a report')
        ];
    }
}
