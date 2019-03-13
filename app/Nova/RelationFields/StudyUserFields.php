<?php

namespace App\Nova\RelationFields;

use App\Policies\Authorization;
use Laravel\Nova\Fields\Select;

class StudyUserFields
{
    public function __invoke()
    {
        return [
            Select::make('Role', 'power')
                ->options([
                    Authorization::SCIENTIST => 'Scientist',
                    Authorization::LABMANAGER => 'Lab Manager',
                    Authorization::MONITOR => 'Monitor'
                ])
                ->displayUsingLabels()
                ->sortable()
        ];
    }
}
