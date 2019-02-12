<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Sparclex\NovaCreatableBelongsTo\CreatableBelongsTo;
use Treestoneit\BelongsToField\BelongsToField;

class PrimerMix extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\PrimerMix';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static $search = ['name'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')
                ->rules('required', 'unique:primer_mixes,name,{{resourceId}}')
                ->sortable(),
            CreatableBelongsTo::make('Creator', 'creator', Person::class),
            BelongsToField::make('Reagent'),
            Number::make('Expires in')
                ->help('Amount of days until the primer mix expires')
                ->rules('required')
                ->displayUsing(function ($value) {
                    return sprintf('%d days', $value);
                })
                ->sortable(),
            Number::make('Volume')
                ->help('Volume in μl (microliter)')
                ->rules('required', 'numeric')
                ->displayUsing(function ($value) {
                    return sprintf('%d μl', $value);
                })
                ->sortable()

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
