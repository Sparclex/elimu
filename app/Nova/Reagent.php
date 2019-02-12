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
    public static $model = 'App\Models\Reagent';

    public static $search = ['id', 'lot', 'name'];

    public static $title = 'lot';

    public function subtitle()
    {
        return sprintf('%s | %s', $this->name, $this->expires_at->format('d.m.Y'));
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Lot')
                ->rules('required', 'unique:reagents,lot,{{resourceId}}')
                ->sortable(),
            Text::make('Name')
                ->rules('required')
                ->sortable(),
            Date::make('Expires at')
                ->sortable()
        ];
    }
}
