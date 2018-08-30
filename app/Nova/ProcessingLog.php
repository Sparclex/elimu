<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class ProcessingLog extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\ProcessingLog';

    public static $with = ['test'];

    public static $globallySearchable = false;

    public static $displayInNavigation = false;

    public static function label() {
        return 'Processing Logs';
    }


    public function title() {
        return $this->test->name . " (Brady Nr. " . $this->sample->brady_number . ")";
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->hideFromIndex()->hideFromDetail(),
            BelongsTo::make('Sample')->rules('required','exists:samples,id')->searchable()->sortable(),
            BelongsTo::make('Test')->rules('exists:tests,id')->searchable(),
            DateTime::make('Processed at')->hideFromIndex()
                ->rules('required','date'),
            BelongsTo::make('Receiver', 'receiver', Person::class)->rules('required','exists:people,id')->hideFromIndex()->searchable(),
            BelongsTo::make('Deliverer', 'deliverer', Person::class)->rules('required','exists:people,id')->hideFromIndex()->searchable(),
            BelongsTo::make('Collector', 'collector', Person::class)->rules('required','exists:people,id')->hideFromIndex()->searchable(),
            Trix::make('Comment')->hideFromIndex(),
            HasMany::make('Results')->hideFromIndex()
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
