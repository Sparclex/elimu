<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Treestoneit\BelongsToField\BelongsToField;

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
        return [
            ID::make()
                ->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->creationRules('required', 'unique:assays,name')
                ->updateRules('required', 'unique:assays,name,{{resourceId}}'),
            BelongsToField::make('Definition File', 'definitionFile', AssayDefinitionFile::class),
            BelongsToField::make('Instrument'),
            BelongsToField::make('Protocol'),
            BelongsToField::make('Primer Mix', 'primerMix', PrimerMix::class),
            Trix::make('Description'),
            HasMany::make('Results')
        ];
    }
}
