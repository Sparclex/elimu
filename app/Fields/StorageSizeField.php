<?php

namespace App\Fields;

use Laravel\Nova\Fields\Number;

class StorageSizeField
{
    public function __invoke()
    {
        return [
            Number::make('Box Size', 'size')->rules('required', 'numeric'),
        ];
    }
}
