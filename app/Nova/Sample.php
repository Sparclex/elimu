<?php

namespace App\Nova;

use App\Fields\HtmlReadonly;
use App\Importer\SampleImporter;
use App\Nova\Lenses\AssayResults;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Treestoneit\BelongsToField\BelongsToField;

class Sample extends Resource
{
    public static $model = \App\Models\Sample::class;

    public static $search = ['sample_id', 'subject_id', 'visit_id'];

    public static $title = 'sample_id';

    public static $importer = SampleImporter::class;


    public function subtitle()
    {
        return 'Study: (' . $this->study->study_id . ') ' . $this->study->name;
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->onlyOnForms(),
            BelongsToField::make('Study')
                ->searchable()
                ->rules('required')
                ->onlyOnDetail(),
            Text::make('Sample ID')
                ->creationRules('required', 'unique:samples,sample_id')
                ->updateRules('required', 'unique:samples,sample_id,{{resourceId}}')
                ->sortable(),
            Text::make('Subject ID')
                ->sortable(),
            Text::make('Visit', 'visit_id')
                ->sortable(),
            DateTime::make('Collected at')
                ->sortable(),
            Date::make('Birthdate')
                ->rules('nullable', 'date')
                ->hideFromIndex(),
            Select::make('Gender')
                ->options([0 => 'Male', 1 => 'Female'])
                ->hideFromIndex(),

            BelongsToMany::make('Types', 'sampleTypes', SampleType::class)

        ];
    }
}
