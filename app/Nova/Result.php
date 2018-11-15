<?php

namespace App\Nova;

use App\Fields\Status;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

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
            BelongsTo::make('Sample'),
            BelongsTo::make('Experiment'),
            Text::make('Target'),
            Text::make('Value')
                ->exceptOnForms(),
            Status::make('Status')
                ->loadingWhen('Pending')
                ->successWhen('Verified'),

            HasMany::make('Data', 'resultData', ResultData::class),

        ];
    }
}
