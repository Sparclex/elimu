<?php

namespace App\Nova;

use App\Fields\StorageSizeField;
use App\Nova\RelationFields\SampleMutationFields;
use App\Rules\StudyUnique;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class SampleType extends Resource
{
    public static $model = 'App\Models\SampleType';

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public static function singularLabel()
    {
        return 'Sample Type';
    }

    public static function label()
    {
        return 'Sample Types';
    }

    public function fields(Request $request)
    {
        $formats = array_combine(['ABC', 'abc', '123'], ['ABC', 'abc', '123']);
        return [
            ID::make()->onlyOnForms(),
            Text::make('Name')
                ->sortable()
                ->rules('required', (new StudyUnique('sample_types', 'name'))
                    ->ignore($request->resourceId)),
            Number::make('Columns')
                ->rules('required_with:rows')
                ->hideFromIndex(),
            Select::make('Column Format')
                ->options($formats)
                ->hideFromIndex(),
            Number::make('Rows')
                ->rules('required_with:columns')
                ->hideFromIndex(),
            Select::make('Row Format')
                ->options($formats)
                ->hideFromIndex(),
            HasMany::make('Storage', 'storages', Storage::class),
            BelongsToMany::make('Samples', 'samples', Sample::class)
                ->fields(new SampleMutationFields)
        ];
    }
}
