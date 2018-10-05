<?php

namespace App\Nova;

use App\Actions\RequestExperiment;
use App\Exports\SampleExport;
use App\Fields\DataPanel;
use App\Fields\DownloadReport;
use App\Fields\SampleStatusField;
use App\Nova\Lenses\SampleRegistry;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class Sample extends Resource
{
    use RelationSortable;

    public static $globallySearchable = false;

    public static $with = ['sampleInformation'];
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
        return $this->sampleInformation->sample_id;
    }

    public function subtitle()
    {
        return 'Study: (' . $this->sampleInformation->study->study_id . ') ' . $this->sampleInformation->study->name;
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
                'unique:samples,sample_type_id,NULL,id,sample_information_id,' . $sampleInformationId
            )->sortable(),
            BelongsTo::make('Sample Information', 'sampleInformation', SampleInformation::class)->rules(
                'required'
            )->sortable(),
            Number::make('Quantity', 'quantity')->rules(
                'nullable',
                'numeric',
                'existing_storage:study,sampleType'
            )->help(
                'Enter 0 if this sample should not be stored.'
            )->sortable(),
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
        return [
            new SampleRegistry()
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new RequestExperiment(),
            (new DownloadExcel())->withHeadings()
        ];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return self::sortByMultiple($request, $query, [
            ['sampleInformation', 'sample_id'],
            ['sampleType']
        ]);
    }
}
