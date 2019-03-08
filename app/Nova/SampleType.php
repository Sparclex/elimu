<?php

namespace App\Nova;

use App\Fields\StorageSizeField;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
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
        return [
            ID::make()->onlyOnForms(),
            Text::make('Name')
                ->sortable()
                ->creationRules('required', 'unique:sample_types,name')
                ->updateRules('required', 'unique:sample_types,name,{{resourceId}}'),
            BelongsToMany::make('Study', 'studies', Study::class)
                ->fields(new StorageSizeField),
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
