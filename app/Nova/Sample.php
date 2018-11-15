<?php

namespace App\Nova;

use App\Actions\RequestExperiment;
use App\Fields\DataPanel;
use App\Fields\DownloadReport;
use App\Nova\Invokables\ResultFields;
use App\Nova\Lenses\SampleRegistry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class Sample extends Resource
{
    use RelationSortable;

    public static $globallySearchable = false;

    public static $with = ['sampleInformation'];

    public static $model = 'App\Models\Sample';

    public static $search = [];

    public function title()
    {
        return $this->sampleInformation->sample_id;
    }

    public function subtitle()
    {
        return 'Study: (' . $this->sampleInformation->study->study_id . ') ' . $this->sampleInformation->study->name;
    }

    public function fields(Request $request)
    {
        $sampleInformationId = $request->get('sampleInformation');
        return [
            ID::make()
                ->sortable()
                ->hideFromIndex()
                ->hideFromDetail(),
            BelongsTo::make('Type', 'sampleType', SampleType::class)
                ->rules(
                    'required',
                    Rule::unique('samples', 'sample_type_id')
                    ->where('sample_information_id', $sampleInformationId)
                    ->ignore($request->resourceId)
                )
                ->sortable(),
            BelongsTo::make('Sample Information', 'sampleInformation', SampleInformation::class)
                ->rules('required')
                ->sortable(),
            Number::make('Quantity', 'quantity')
                ->rules('nullable', 'numeric', 'existing_storage:study,sampleType')
                ->help('Enter 0 if this sample should not be stored.')
                ->sortable(),
            DownloadReport::make($this->id),

            BelongsToMany::make('Experiments'),
            HasMany::make('Results')
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return self::sortByMultiple($request, $query, [
            ['sampleInformation', 'sample_id'],
            ['sampleType']
        ]);
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
}
