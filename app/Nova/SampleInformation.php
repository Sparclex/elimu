<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use App\Fields\HtmlReadonly;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use App\Nova\Filters\CollectedAfter;
use App\Nova\Filters\CollectedBefore;
use App\Importer\SampleInformationImporter;
use Sparclex\NovaImportCard\NovaImportCard;
use Treestoneit\BelongsToField\BelongsToField;

class SampleInformation extends Resource
{
    public static $model = 'App\Models\SampleInformation';

    public static $search = ['sample_id', 'subject_id', 'visit_id'];

    public static $title = 'sample_id';

    public static $importer = SampleInformationImporter::class;

    public static function label()
    {
        return 'Sample Informations';
    }

    public static function singularLabel()
    {
        return 'Sample Information';
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
            BelongsToField::make('Study')
                ->searchable()
                ->rules('required')
                ->onlyOnDetail(),
            Text::make('Sample ID')
                ->creationRules('required', 'unique:sample_informations,sample_id')
                ->updateRules('required', 'unique:sample_informations,sample_id,{{resourceId}}')
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
            HasMany::make('Samples')

        ];
    }

    public function cards(Request $request)
    {
        return [
            new NovaImportCard(static::class)
        ];
    }

    public function filters(Request $request)
    {
        return [
            //new CollectedAfter(),
            //new CollectedBefore()
        ];
    }
}
