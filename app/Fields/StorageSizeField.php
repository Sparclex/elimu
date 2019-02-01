<?php

namespace App\Fields;

use Laravel\Nova\Fields\Number;

class StorageSizeField
{
    public function __invoke()
    {
        return [
            Number::make('Columns')->rules('required', 'numeric'),
            Number::make('Rows')->rules('required', 'numeric'),
        ];
    }
}
