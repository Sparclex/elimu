<?php

namespace App\Nova;

use App\Fields\CustomBelongsToMany;
use App\Fields\SampleIds;
use App\Nova\Invokables\DeleteExperimentFile;
use App\Nova\Invokables\UpdateExperimentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Storage as StorageFacade;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Trix;
use Treestoneit\BelongsToField\BelongsToField;

class Experiment extends Resource
{
    use RelationSortable;

    public static $model = 'App\Models\Experiment';

    public static $search = ['id'];
    public static $globallySearchable = false;

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            SampleIds::make('Samples')
                ->help('A new line for each sample id')
                ->rules('required'),
            BelongsToField::make('Assay'),
            DateTime::make('Requested at')
                ->rules('required', 'date')
                ->hideWhenCreating()
                ->sortable(),
            Number::make('Number of Samples', 'samples_count')
                ->onlyOnIndex()
                ->sortable(),
            Trix::make('Comment')
                ->hideFromIndex(),

            CustomBelongsToMany::make('Samples'),

            HasMany::make('Result Data', 'resultData', ResultData::class),

            File::make('Result File')
                ->hideWhenCreating()
                //->prunable()
                ->resolveUsing(function () {
                    return $this->original_filename;
                })
                ->store(new UpdateExperimentResult)
                ->delete(new DeleteExperimentFile)
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
