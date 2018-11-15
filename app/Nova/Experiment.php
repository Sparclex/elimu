<?php

namespace App\Nova;

use App\Nova\Invokables\DeleteExperimentFile;
use App\Nova\Invokables\ResultFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as StorageFacade;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Experiment extends Resource
{
    use RelationSortable;

    public static $with = ['reagent', 'reagent.assay', 'requester'];

    public static $model = 'App\Models\Experiment';

    public static $search = [
        'name',
    ];
    public static $globallySearchable = false;

    public static function indexQuery(NovaRequest $request, $query)
    {
        return self::sortByMultiple($request, $query, [
            ['reagent', 'lot'],
            ['requester', 'name']
        ]);
    }

    public function title()
    {
        return "Experiment: " . $this->id . " (" . $this->reagent->assay->name . ")";
    }

    public function fields(Request $request)
    {
        $resultTypes = array_keys(config('lims.result_types'));
        return [
            ID::make()
                ->hideFromIndex(),
            BelongsTo::make('Assay', 'reagent', Reagent::class)
                ->hideWhenUpdating()
                ->sortable(),
            BelongsTo::make('Study')
                ->onlyOnDetail(),
            BelongsTo::make('Requester', 'requester', User::class)
                ->rules('required', 'exists:people,id')
                ->searchable()
                ->hideWhenUpdating()
                ->sortable(),
            DateTime::make('Requested at')
                ->rules('required', 'date')
                ->hideWhenUpdating()
                ->sortable(),
            Trix::make('Comment')
                ->hideFromIndex(),

            BelongsToMany::make('Samples'),

            HasMany::make('Results'),

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
                        new $handler($this->id, 'result_file', $request->result_file);
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
