<?php

namespace App\Nova;

use App\Nova\RelationFields\ConcentrationPivotField;
use App\Rules\StudyUnique;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Treestoneit\BelongsToField\BelongsToField;

class Control extends Resource
{
    public static $model = 'App\Models\Control';

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
                ->rules(
                    'required',
                    (new StudyUnique('assays', 'name'))->ignore($request->resourceId)
                ),
            Trix::make('Description'),
            Text::make('Concentration'),
            BelongsToMany::make('Assays')
                ->searchable(),
        ];
    }
}
