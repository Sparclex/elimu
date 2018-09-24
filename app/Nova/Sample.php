<?php

namespace App\Nova;

use App\Actions\ChangeValidationStatus;
use App\Actions\GenerateReport;
use App\Actions\RequestExperiment;
use App\Fields\Data;
use App\Fields\DataPanel;
use App\Fields\DownloadReport;
use App\Fields\SampleDataFields;
use App\Fields\SampleStatusField;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Panel;

class Sample extends Resource
{
    public static $globallySearchable = false;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Sample';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    public function title()
    {
        return "Brady Nr. ".$this->sampleInformation->sample_id;
    }

    public function subtitle()
    {
        return 'Study: ('.$this->study->study_id.') '.$this->study->name;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $sampleInformationId = $request->get('sampleInformation');

        return [
            ID::make()->sortable()->hideFromIndex()->hideFromDetail(),
            BelongsTo::make('Type', 'sampleType', SampleType::class)->rules(
                'required',
                'unique:samples,sample_type_id,NULL,id,sample_information_id,'.$sampleInformationId),
            BelongsTo::make('Sample Information', 'sampleInformation', SampleInformation::class)->rules(
                'required'),
            Number::make('Quantity', 'quantity')->rules(
                'nullable', 'numeric', 'existing_storage:study,sampleType')->help(
                'Enter 0 if this sample should not be stored.'),
            DownloadReport::make($this->id),
            HasMany::make('Data', 'data', SampleData::class),
            BelongsToMany::make('Experiments')
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [new RequestExperiment()];
    }
}
