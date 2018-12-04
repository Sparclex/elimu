<?php

namespace App\Nova;

use App\Fields\Status;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use App\Nova\Lenses\InvalidResults;
use Treestoneit\BelongsToField\BelongsToField;

class Result extends Resource
{
    public static $displayInNavigation = false;

    public static $model = 'App\Models\Result';

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static $with = ['assay.inputParameter','sample.sampleInformation', 'resultData'];

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->hideFromIndex(),
            BelongsToField::make('Sample'),
            BelongsToField::make('Assay'),
            Text::make('Target')
                ->sortable(),
            Text::make('Value'),
            Status::make('Status')
                ->loadingWhen('Pending')
                ->successWhen('Verified'),

            HasMany::make('Data', 'resultData', ResultData::class),

        ];
    }

    public function lenses(Request $request)
    {
        return [
            new InvalidResults,
        ];
    }
}
