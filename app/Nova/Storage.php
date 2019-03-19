<?php

namespace App\Nova;

use App\Support\Position;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Treestoneit\BelongsToField\BelongsToField;

class Storage extends Resource
{
    public static $globallySearchable = false;

    public static $model = 'App\Models\Storage';

    public static $title = 'id';

    public static $with = ['sample', 'sampleType'];

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
            BelongsToField::make('Type', 'sampleType', SampleType::class),
            Number::make('Position')->resolveUsing(function () {
                return Position::fromPosition($this->position)
                    ->withColumns($this->sampleType->columns)
                    ->withRows($this->sampleType->rows)
                    ->showPlates()
                    ->toLabel();
            })->sortable(),
        ];
    }
}
