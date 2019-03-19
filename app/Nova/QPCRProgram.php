<?php

namespace App\Nova;

use App\Fields\Table;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class QPCRProgram extends Resource
{
    public static $globallySearchable = true;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\QPCRProgram';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    public static function label()
    {
        return 'QPCR Programs';
    }

    public static function singularLabel()
    {
        return 'QPCR Program';
    }


    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')
                ->rules('required'),
            Table::make('Program')
                ->fields([
                    Text::make('Step'),
                    Text::make('Temp(°C)', 'temp'),
                    Text::make('Time (s)', 'time'),
                    Text::make('Goto'),
                    Text::make('Cycles')
                ]),
            Table::make('Detection Table')
                ->fields([
                    Text::make('Channel ID', 'channel_id'),
                    Text::make('Target'),
                    Text::make('Threshold')
                ]),

            BelongsToMany::make('Assays')
                ->searchable(),
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
