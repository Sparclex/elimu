<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;

class Assay extends Resource
{
    public static $model = 'App\Models\Assay';

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public static $globallySearchable = true;

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
            Select::make('Result Type')
                ->options(array_combine($resultTypes, $resultTypes))
                ->rules('required', 'in:' . implode(',', $resultTypes)),
            Trix::make('Description'),
            HasMany::make('Results')
        ];
    }
}
