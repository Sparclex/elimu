<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Treestoneit\BelongsToField\BelongsToField;

class Storage extends Resource
{
    public static $model = 'App\Models\Storage';

    public static $title = 'id';

    public static $with = ['sample'];

    public static $search = [
        'id',
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
            BelongsToField::make('Study')
                ->onlyOnDetail(),
            BelongsToField::make('Sample'),
            BelongsToField::make('SampleType'),
            Number::make('Box'),
            Number::make('Position'),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->getQuery()->orders = [];
        return $query->orderBy('study_id')
            ->orderBy('sample_type_id')
            ->orderByDesc('box')
            ->orderByDesc('position');
    }
}
