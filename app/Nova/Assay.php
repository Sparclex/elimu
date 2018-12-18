<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\HasMany;

class Assay extends Resource
{
    public static $model = 'App\Models\Assay';

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public static $globallySearchable = false;

    public function fields(Request $request)
    {
        $resultTypes = array_keys(config('lims.result_types'));
        return [
            ID::make()
                ->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->creationRules('required', 'unique:assays,name')
                ->updateRules('required', 'unique:assays,name,{{resourceId}}'),
            Text::make('SOP')
                ->rules('required')
                ->sortable(),
            Select::make('Result Type')
                ->hideFromIndex()
                ->options(array_combine($resultTypes, $resultTypes))
                ->rules('required', 'in:' . implode(',', $resultTypes)),
            Trix::make('Description'),
            HasOne::make('Input Parameters', 'inputParameter', InputParameter::class),
            HasMany::make('Results')
        ];
    }
}
