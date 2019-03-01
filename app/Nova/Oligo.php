<?php

namespace App\Nova;

use App\Nova\RelationFields\PrimerMixOligoFields;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Oligo extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Oligo';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'oligo_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'oligo_id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable()
                ->hideFromIndex()
                ->hideFromDetail(),
            Text::make('Oligo ID')
                ->rules('required', 'unique:oligos,oligo_id,{{resourceId}}'),
            Text::make('Sequence')
                ->rules('required'),
            Text::make('5\' Modification', '5_prime_modification'),
            Text::make('3\' Modification', '3_prime_modification'),
            Text::make('Species')
                ->rules('required'),
            Text::make('Target Gene')
                ->rules('required'),
            Text::make('Publication')
                ->rules('required'),
            Trix::make('Comment'),

            BelongsToMany::make('PrimerMix')
                ->searchable()
                ->fields(new PrimerMixOligoFields),
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
