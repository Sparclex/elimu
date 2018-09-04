<?php

namespace Sparclex\Lims\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Trix;

class ProcessingLog extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Sparclex\Lims\Models\ProcessingLog';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    public static function label()
    {
        return 'Processing Logs';
    }

    public static function singularLabel()
    {
        return 'Processing Log';
    }

    public function title()
    {
        return $this->processed_at->format('Y-m-d H:i:s');
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
            ID::make()->hideFromIndex()->hideFromDetail(),
            DateTime::make('Processed at')->rules('required', 'date'),
            BelongsTo::make('Receiver', 'receiver', Person::class)->rules('required', 'exists:people,id')->searchable(),
            BelongsTo::make('Deliverer', 'deliverer', Person::class)->rules('required', 'exists:people,id')->searchable(),
            BelongsTo::make('Collector', 'collector', Person::class)->rules('required', 'exists:people,id')->searchable(),
            Trix::make('Comment')->hideFromIndex(),
            HasMany::make('Experiment')->rules('exists:experiments,id'),
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
