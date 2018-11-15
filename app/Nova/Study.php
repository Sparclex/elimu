<?php

namespace App\Nova;

use App\Fields\StorageSizeField;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Study extends Resource
{
    public static $model = 'App\Models\Study';

    public static $search = [
        'name',
        'study_id',
        'id',
    ];

    public function title()
    {
        return $this->study_id . ": " . str_limit($this->name, 20);
    }


    public function fields(Request $request)
    {
        return [
            ID::make()
                ->onlyOnForms(),
            Text::make('Study ID')
                ->sortable()
                ->creationRules('required', 'unique:studies,study_id')
                ->updateRules('required', 'unique:studies,study_id,{{resourceId}}'),
            Text::make('Name')
                ->sortable()
                ->creationRules('required', 'unique:studies,name')
                ->updateRules('required', 'unique:studies,name,{{resourceId}}'),
            HasMany::make('SampleInformations'),
            BelongsToMany::make('Sample Types', 'sampleTypes', SampleType::class)
                ->fields(new StorageSizeField),
            BelongsToMany::make('Users'),
        ];
    }
}
