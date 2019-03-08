<?php

namespace App\Nova;

use App\Fields\StorageSizeField;
use App\Rules\StudyUnique;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Treestoneit\BelongsToField\BelongsToField;

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
        return [
            ID::make()->onlyOnForms(),
            Text::make('Name')
                ->sortable()
                ->rules('required', (new StudyUnique('sample_types', 'name'))
                    ->ignore($request->resourceId)),
            Number::make('Columns')
                ->rules('required_with:rows'),
            Number::make('Rows')
                ->rules('required_with:columns'),
            HasMany::make('Storage', 'storages', Storage::class),

            BelongsToMany::make('Samples', 'samples', Sample::class)
                ->fields(function () {
                    return [
                        Text::make('Aliquots', 'quantity')
                    ];
                })
        ];
    }
}
