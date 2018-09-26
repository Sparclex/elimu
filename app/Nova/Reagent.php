<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Reagent extends Resource
{
    public static $globallySearchable = false;
    public static $displayInNavigation = false;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Reagent';
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    public function title()
    {
        return $this->assay->name . " (" . $this->lot . ")";
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
            ID::make()->sortable(),
            BelongsTo::make('Assay'),
            HasMany::make('Experiments'),
            Text::make('Lot'),
            Text::make('Name'),
            Date::make('Expires at', 'expires_at')
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
