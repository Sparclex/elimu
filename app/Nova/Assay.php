<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
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
            Trix::make('Description'),
            HasOne::make('Input Parameters', 'inputParameter', InputParameter::class),
            HasMany::make('Results')
        ];
    }
}
