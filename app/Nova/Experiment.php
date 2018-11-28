<?php

namespace App\Nova;

use App\Nova\ResultData;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Treestoneit\BelongsToField\BelongsToField;
use App\Nova\Invokables\ResultFields;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Invokables\DeleteExperimentFile;
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
        return $query->withRequesterName()->withAssayName();
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
        $resultTypes = array_keys(config('lims.result_types'));
        return [
            ID::make()->sortable(),
            Text::make('Assay', 'assay_name')
                ->exceptOnForms()
                ->sortable(),
            BelongsToField::make('Study')
                ->onlyOnDetail(),
            Text::make('Requester', 'requester_name')
                ->exceptOnForms()
                ->sortable(),
            DateTime::make('Requested at')
                ->rules('required', 'date')
                ->hideWhenUpdating()
                ->sortable(),
            Trix::make('Comment')
                ->hideFromIndex(),

            BelongsToMany::make('Samples'),

            HasMany::make('Result Data', 'resultData', ResultData::class),

            File::make('Result File')
                ->hideWhenCreating()
                ->prunable()
                ->delete(new DeleteExperimentFile)
                ->resolveUsing(function () {
                    return $this->original_filename;
                })
                ->store(
                    function (Request $request) {
                        $handler = (config('lims.result_types')[$request->file_type]);
                        new $handler($this->resource, 'result_file', $request->result_file);
                        if ($this->result_file) {
                            StorageFacade::disk('local')->delete($this->result_file);
                        }
                        return [
                            'result_file' => $request->result_file->store('experiments'),
                            'original_filename' => $request->result_file->getClientOriginalName(),
                            'result_type' => $request->file_type
                        ];
                    }
                )
                ->download(function ($request, $model) {
                    return StorageFacade::download($model->result_file, $model->original_filename);
                }),
            Select::make('File Type')
                ->onlyOnForms()
                ->hideWhenCreating()
                ->options(array_combine($resultTypes, $resultTypes))
                ->rules('required_with:result_file', 'in:' . implode(',', $resultTypes))

        ];
    }
}
