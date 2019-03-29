<?php

namespace App\Nova\RelationFields;

use Laravel\Nova\Fields\Number;

class SampleMutationFields
{
    public function __invoke()
    {
        return [
            Number::make('Aliquots', 'quantity'),
        ];
    }
}
