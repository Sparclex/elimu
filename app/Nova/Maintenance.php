<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Trix;
use Sparclex\NovaCreatableBelongsTo\CreatableBelongsTo;
use Treestoneit\BelongsToField\BelongsToField;

class Maintenance extends Resource
{
    public static $globallySearchable = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Maintenance';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable(),
            BelongsToField::make('Instrument'),
            CreatableBelongsTo::make('Technician', 'technician', Person::class),
            Date::make('Date')
                ->rules('required')
                ->sortable(),
            Trix::make('Procedure')
        ];
    }
}
