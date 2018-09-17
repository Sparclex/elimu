<?php

namespace App\Nova;

use App\Rules\DataFile;
use App\Utility\RDML;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Trix;

class Experiment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Experiment';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @return string
     */
    public function title()
    {
        return "Experiment: ".$this->id." (".$this->assay->name.")";
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    public static $globallySearchable = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {


        return array_merge(
            [
                ID::make()->hideFromIndex(),
                BelongsTo::make('Assay')->hideWhenUpdating(),
                BelongsTo::make('Requester', 'requester', User::class)->rules(
                    'required', 'exists:people,id')->searchable()->hideWhenUpdating(),
                DateTime::make('Requested at')->rules('required', 'date')->hideWhenUpdating(),
                Trix::make('Comment')->hideFromIndex(),

                BelongsToMany::make('Samples'),

            ], $this->dataFields($request));
    }

    public function dataFields(Request $request)
    {

        return [
            File::make('File')->onlyOnForms()->prunable()->store(
                function (Request $request) {
                    return ['file' => RDML::toXml($request->file('file'), $request->experiment)];
                })->creationRules('required', new DataFile($request->experiment))->updateRules('file'),
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
