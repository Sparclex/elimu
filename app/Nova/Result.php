<?php

namespace App\Nova;

use App\Nova\Lenses\InvalidResults;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Treestoneit\BelongsToField\BelongsToField;

class Result extends Resource
{
    public static $displayInNavigation = false;

    public static $model = 'App\Models\Result';

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->hideFromIndex(),
            BelongsToField::make('Sample'),
            BelongsToField::make('Assay'),
            Text::make('Type', function () {
                return $this->assay->definitionFile->sampleType->name;
            }),
            Text::make('Target')
                ->sortable(),

            HasMany::make('Data', 'resultData', ResultData::class),
        ];
    }
}
