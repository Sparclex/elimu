<?php

namespace Sparclex\Lims\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Sparclex\Lims\Fields\HtmlReadonly;

class Sample extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Sparclex\Lims\Models\Sample';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'subject_id',
    ];

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
        return [
            ID::make()->sortable()->hideFromIndex()->hideFromDetail(),
            BelongsTo::make('Study')->searchable()->rules('required', 'exists:studies,id'),
            BelongsTo::make('Type', 'sampleType', SampleType::class)->rules('required', 'exists:sample_types,id'),
            BelongsTo::make('Sample Information', 'sampleInformation', SampleInformation::class)->rules('required', 'exists:sample_informations,id'),
            Number::make('Quantity', 'quantity')->rules('nullable','numeric')->help('Enter 0 if this sample should not be stored.')
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
        return [];
    }
}
