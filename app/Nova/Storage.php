<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Treestoneit\BelongsToField\BelongsToField;

class Storage extends Resource
{
    public static $model = 'App\Models\Storage';

    public static $title = 'id';

    public static $with = ['sample.sampleInformation'];

    public static $search = [
        'box',
        'position',
    ];

    public static function label()
    {
        return 'Storage';
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->onlyOnForms(),
            BelongsToField::make('Sample'),
            Number::make('Box'),
            Number::make('Position'),
        ];
    }
}
