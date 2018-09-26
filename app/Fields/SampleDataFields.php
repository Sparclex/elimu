<?php

namespace App\Fields;

use Laravel\Nova\Fields\Number;

class SampleDataFields
{
    public function __invoke()
    {
        return [
            Status::make('Status')
                ->loadingWhen('Pending')
                ->failedWhen('Declined')
                ->successWhen('Accepted')->sortable(),
            Number::make('Cq', function () {
                return 4.4;
            })
        ];
    }
}
