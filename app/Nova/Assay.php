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
                ->rules(
                    'required',
                    (new StudyUnique('assays', 'name'))->ignore($request->resourceId)
                ),
            BelongsToField::make('Creator', 'creator', Person::class)->exceptOnForms(),
            BelongsToField::make('Definition File', 'definitionFile', AssayDefinitionFile::class),
            BelongsToField::make('Instrument'),
            BelongsToField::make('SOP', 'protocol', Protocol::class),
            BelongsToField::make('Reagent')->nullable(),
            Number::make('Reaction Volume', 'reaction_volume')
                ->step(0.01),
            BelongsToMany::make('Oligos')
                ->searchable()
                ->fields(new ConcentrationPivotField),
            BelongsToMany::make('Controls')
                ->searchable(),
            Trix::make('Description'),
            HasMany::make('Results')
        ];
    }
}
