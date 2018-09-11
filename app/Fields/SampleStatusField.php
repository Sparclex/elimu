<?php

namespace App\Fields;


use Laravel\Nova\Fields\Text;

class SampleStatusField
{
    public function __invoke()
    {
        return [
            Status::make('Status')
                ->loadingWhen('Pending')
                ->failedWhen('Declined')
                ->successWhen('Accepted')->sortable(),
        ];
    }
}
