<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use App\Fields\CustomBelongsToMany;
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

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->getQuery()->orders = [];
        return $query
            ->orderBy('sample_type_id')
            ->orderby('box')
            ->orderBy('position');
    }
}
