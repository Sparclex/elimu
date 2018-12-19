<?php

namespace App\Nova;

use App\Fields\CustomBelongsToMany;
use App\Fields\ReagentsFields;
use App\Fields\SampleIds;
use App\Fields\SampleTypeField;
use App\Nova\Invokables\UpdateExperimentResult;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Invokables\DeleteExperimentFile;
use Treestoneit\BelongsToField\BelongsToField;
use App\Models\Assay as AssayModel;
use Illuminate\Support\Facades\Storage as StorageFacade;

class Experiment extends Resource
{
    use RelationSortable;

    public static $model = 'App\Models\Experiment';

    public static $search = [
        'id', 'assay_name'
    ];
    public static $globallySearchable = false;

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->withRequesterName()->withAssayName()->withCount('samples');
    }

    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query->withRequesterName()->withAssayName();
    }
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query)->withRequesterName()->withAssayName();
    }

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            SampleTypeField::make('Sample Type', 'sampleType', SampleType::class)
                ->rules('required'),
            SampleIds::make('Samples')
                ->help('A new line for each sample id')
                ->rules('required'),
            ReagentsFields::make(AssayModel::all())
                ->onlyOnForms()
                ->hideWhenUpdating(),
            Text::make('Assay', 'assay_name')
                ->exceptOnForms()
                ->sortable(),
            BelongsToField::make('Study')
                ->onlyOnDetail(),
            Text::make('Requester', 'requester_name')
                ->exceptOnForms()
                ->sortable(),
            Number::make('Number of Samples', 'samples_count')
                ->onlyOnIndex()
                ->sortable(),
            DateTime::make('Requested at')
                ->rules('required', 'date')
                ->hideWhenUpdating()
                ->sortable(),
            Trix::make('Comment')
                ->hideFromIndex(),

            CustomBelongsToMany::make('Samples'),

            HasMany::make('Result Data', 'resultData', ResultData::class),

            File::make('Result File')
                ->hideWhenCreating()
                ->prunable()
                ->delete(new DeleteExperimentFile)
                ->resolveUsing(function () {
                    return $this->original_filename;
                })
                ->store(new UpdateExperimentResult)
                ->download(function ($request, $model) {
                    return StorageFacade::download($model->result_file, $model->original_filename);
                }),
        ];
    }

    public function cards(Request $request)
    {
        return [];
    }
}
