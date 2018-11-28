<?php

namespace App\Nova;

use App\Fields\DataPanel;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;
use App\Fields\DownloadReport;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\HasMany;
use App\Actions\RequestExperiment;
use App\Nova\Lenses\SampleRegistry;
use App\Nova\Filters\CollectedAfter;
use App\Nova\Filters\CollectedBefore;
use App\Nova\Invokables\ResultFields;
use App\Nova\Filters\SampleTypeFilter;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use Treestoneit\BelongsToField\BelongsToField;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class Sample extends Resource
{
    use RelationSortable;

    public static $model = 'App\Models\Sample';

    public static $globallySearchable = false;

    public static $search = [];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'sampleType' => ['name'],
        'sampleInformation' => ['sample_id']
    ];

    public function title()
    {
        return $this->sampleInformation->sample_id;
    }

    public function subtitle()
    {
        return 'Study: (' . $this->study->study_id . ') ' . $this->sampleInformation->study->name;
    }

    public function fields(Request $request)
    {
        $sampleInformationId = $request->get('sampleInformation');
        return [
            ID::make()
                ->sortable()
                ->hideFromIndex()
                ->hideFromDetail(),
            Text::make('Type', 'sample_type_name')
                ->exceptOnForms()
                ->sortable(),
            BelongsToField::make('Type', 'sampleType', SampleType::class)
                ->rules(
                    'required',
                    Rule::unique('samples', 'sample_type_id')
                    ->where('sample_information_id', $sampleInformationId)
                    ->ignore($request->resourceId)
                )
                ->onlyOnForms(),
            Text::make('Sample ID', 'sample_id')
                ->exceptOnForms()
                ->sortable(),
            BelongsToField::make('Sample ID', 'sampleInformation', SampleInformation::class)
                ->rules('required')
                ->onlyOnForms(),
            Number::make('Quantity', 'quantity')
                ->rules('nullable', 'numeric', 'existing_storage:study,sampleType')
                ->help('Enter 0 if this sample should not be stored.')
                ->sortable(),

            Code::make('Extra')->json(),
           // DownloadReport::make($this->id),

            BelongsToMany::make('Experiments'),
            HasMany::make('Results')
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query
            ->with('sampleInformation', 'sampleType')
            ->withType()
            ->withSampleId();
    }

    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query)
            ->with('sampleInformation', 'sampleType')
            ->withType()
            ->withSampleId();
    }

    public static function scoutQuery(NovaRequest $request, $query)
    {
        return parent::scoutQuery($request, $query)
            ->with('sampleInformation', 'sampleType')
            ->withType()
            ->withSampleId();
    }

    public function lenses(Request $request)
    {
        return [
            new SampleRegistry()
        ];
    }

    public function actions(Request $request)
    {
        return [
            new RequestExperiment(),
            (new DownloadExcel())->withHeadings()
        ];
    }

    public function filters(Request $request)
    {
        return [
            //new CollectedAfter('sampleInformation'),
            //new CollectedBefore('sampleInformation'),
            //new SampleTypeFilter()
        ];
    }
}
