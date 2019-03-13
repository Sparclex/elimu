<?php

namespace App\Nova;

use App\Nova\RelationFields\ConcentrationPivotField;
use App\Rules\StudyUnique;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;

class Oligo extends Resource
{

    public static $perPageViaRelationship = 15;
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
     * @param  \Illuminate\Http\Request $request
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
                ->rules(
                    'required',
                    (new StudyUnique('oligos', 'oligo_id'))->ignore($request->resourceId)
                )
                ->sortable(),
            Text::make('Sequence')
                ->rules('required')
                ->sortable(),
            Text::make('5\' Modification', '5_prime_modification')
                ->sortable(),
            Text::make('3\' Modification', '3_prime_modification')
                ->sortable(),
            Text::make('Species')
                ->rules('required')
                ->sortable(),
            Text::make('Target Gene')
                ->rules('required')
                ->sortable(),
            Text::make('Publication')
                ->rules('required')
                ->hideFromIndex()
                ->sortable(),
            Trix::make('Comment'),

            BelongsToMany::make('Assays')
                ->searchable()
                ->fields(new ConcentrationPivotField),
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
