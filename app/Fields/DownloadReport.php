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
        $experimentsIds = DB::table('sample_data')
            ->where('sample_id', $this->sampleId)
            ->where('status', '<>', 'Pending')
            ->pluck('experiment_id', 'experiment_id');
        if (!$experimentsIds->count()) {
            return [];
        }
        return [
            Select::make('Experiment')
                ->options($experimentsIds->toArray())
                ->rules('required', 'exists:experiments,id')
                ->help('Only accepted sample data can be used to generate a report')
        ];
    }
}
