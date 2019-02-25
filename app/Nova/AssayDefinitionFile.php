<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Treestoneit\BelongsToField\BelongsToField;

class AssayDefinitionFile extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\AssayDefinitionFile';

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
        return 'Assay Definition Files';
    }

    public static function singularLabel()
    {
        return 'Assay Definition File';
    }


    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $resultTypes = array_keys(config('lims.result_types'));

        return [
            ID::make()->sortable(),
            Text::make('Name')
                ->rules('required', 'unique:assay_definition_files,name,{{resourceId}}'),
            BelongsToField::make('Sample Type', 'sampleType', SampleType::class),
            Select::make('Result Type', 'result_type')
                ->options(array_combine($resultTypes, $resultTypes))
                ->rules('required', Rule::in($resultTypes)),
            File::make('Path')
                ->disk('local')
                ->path('assay-definition-files')
                ->storeOriginalName('original_name')
                ->rules('required')
                ->deletable(false),
            Code::make('Parameters')
                ->onlyOnDetail()
                ->json()
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
