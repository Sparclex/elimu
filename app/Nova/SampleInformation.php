<?php

namespace App\Nova;

use App\Fields\HtmlReadonly;
use App\Nova\Filters\CollectedAfter;
use App\Nova\Filters\CollectedBefore;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class SampleInformation extends Resource
{
    public static $model = 'App\Models\SampleInformation';

    public static $search = ['sample_id', 'subject_id', 'visit_id'];

    public static function label()
    {
        return 'Sample Informations';
    }

    public static function singularLabel()
    {
        return 'Sample Information';
    }

    public function title()
    {
        return "Sample ID " . $this->sample_id;
    }

    public function subtitle()
    {
        return 'Study: (' . $this->study->study_id . ') ' . $this->study->name;
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->onlyOnForms(),
            BelongsTo::make('Study')
                ->searchable()
                ->rules('required')
                ->onlyOnDetail(),
            Text::make('Sample ID')
                ->creationRules('required', 'unique:sample_informations,sample_id')
                ->updateRules('required', 'unique:sample_informations,sample_id,{{resourceId}}')
                ->sortable(),
            Text::make('Subject ID')
                ->rules('required')
                ->sortable(),
            Text::make('Visit', 'visit_id')
                ->rules('required')
                ->sortable(),
            DateTime::make('Collected at')
                ->rules('required')
                ->sortable(),
            Date::make('Birthdate')
                ->rules('date')
                ->hideFromIndex(),
            Select::make('Gender')
                ->options([0 => 'Male', 1 => 'Female'])
                ->hideFromIndex(),
            HasMany::make('Samples')

        ];
    }

    public function filters(Request $request)
    {
        return [
            new CollectedAfter(),
            new CollectedBefore()
        ];
    }
}
