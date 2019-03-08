<?php

namespace App\Nova;

use App\Fields\StorageSizeField;
use App\Fields\StudyUserFields;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;

class Study extends Resource
{
    public static $displayInNavigation = true;

    public static $model = 'App\Models\Study';

    public static $search = [
        'name',
        'study_id',
        'id',
    ];

    public static $title = 'study_id';

    public function subtitle()
    {
        return $this->name;
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
            Trix::make('Description'),

            HasMany::make('Sample Types', 'sampleTypes', SampleType::class),
            BelongsToMany::make('Users')
                ->fields(new StudyUserFields)
        ];
    }
}
