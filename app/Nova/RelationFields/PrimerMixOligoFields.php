<?php

namespace App\Nova\RelationFields;

use Laravel\Nova\Fields\Number;

class PrimerMixOligoFields
{
    public function __invoke()
    {
        return [
            Number::make('Concentration')
        ];
    }
}
