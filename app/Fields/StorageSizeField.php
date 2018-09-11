<?php

namespace App\Fields;

use Laravel\Nova\Fields\Number;

class StorageSizeField
{
    public function __invoke()
    {
        return [
            Number::make('Fields per box', 'size')->rules('required', 'numeric'),
        ];
    }
}
