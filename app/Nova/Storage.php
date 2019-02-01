<?php

namespace App\Nova;

use App\Support\Position;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Treestoneit\BelongsToField\BelongsToField;

class Storage extends Resource
{
    public static $model = 'App\Models\Storage';

    public static $title = 'id';

    public static $with = ['sample'];

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
            Number::make('Position')->resolveUsing(function () {
                return $this->position;
                $boxSize = auth()
                    ->user()
                    ->study
                    ->sampleTypes()
                    ->wherePivot('sample_type_id', $this->sample_type_id)
                    ->first()->pivot;

                return Position::fromPosition($this->position)
                    ->withColumns($boxSize->columns)
                    ->withRows($boxSize->rows)
                    ->showPlates()
                    ->toLabel();
            })->sortable(),
        ];
    }
}
