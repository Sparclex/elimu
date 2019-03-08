<?php

namespace App\Nova\RelationFields;

use Laravel\Nova\Fields\Number;

class ConcentrationPivotField
{
    public function __invoke()
    {
        return [
            Number::make('Concentration')
                ->step(0.01)
        ];
    }
}
