<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Treestoneit\BelongsToField\BelongsToField;

class Reagent extends Resource
{
    public static $globallySearchable = false;

    public static $model = 'App\Models\Reagent';

    public static $search = ['id', 'lot', 'name'];

    public function title()
    {
        return "{$this->name} ({$this->lot})";
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Lot')
                ->rules('required|unique,reagents,lot')
                ->sortable(),
            Text::make('Name')
                ->rules('required')
                ->sortable(),
            Date::make('Expires at')
                ->sortable()
        ];
    }
}
