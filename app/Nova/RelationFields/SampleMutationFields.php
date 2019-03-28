<?php

namespace App\Nova\RelationFields;

use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class SampleMutationFields
{
    public function __invoke()
    {
        return [
            Number::make('Aliquots', 'quantity'),
            Text::make('Storage conditions', 'storage_conditions'),
        ];
    }
}
