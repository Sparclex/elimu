<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Treestoneit\BelongsToField\BelongsToField;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Reagent extends Resource
{
    public static $globallySearchable = false;

    public static $displayInNavigation = false;

    public static $model = 'App\Models\Reagent';

    public static $search = [];

    public function title()
    {
        return $this->assay->name . " (" . $this->lot . ")";
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable(),
            BelongsToField::make('Assay'),
            HasMany::make('Experiments'),
            Text::make('Lot'),
            Text::make('Name'),
            Date::make('Expires at', 'expires_at')
        ];
    }
}
