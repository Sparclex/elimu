<?php

namespace App\Nova;

use App\Nova\Invokables\DeleteExperimentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as StorageFacade;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Trix;

class Experiment extends Resource
{
    public function __construct(\Illuminate\Database\Eloquent\Model $resource)
    {
        parent::__construct($resource);
    }

    public static $with = ['reagent', 'reagent.assay', 'requester'];

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Experiment';
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];
    public static $globallySearchable = false;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @return string
     */
    public function title()
    {
        return "Experiment: " . $this->id . " (" . $this->reagent->assay->name . ")";
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $resultTypes = array_keys(config('lims.result_types'));
        return [
            ID::make()
                ->hideFromIndex(),
            BelongsTo::make('Assay', 'reagent', Reagent::class)
                ->hideWhenUpdating(),
            BelongsTo::make('Study')
                ->onlyOnDetail(),
            BelongsTo::make('Requester', 'requester', User::class)
                ->rules(
                    'required',
                    'exists:people,id'
                )
                ->searchable()
                ->hideWhenUpdating(),
            DateTime::make('Requested at')
                ->rules('required', 'date')
                ->hideWhenUpdating(),
            Trix::make('Comment')
                ->hideFromIndex(),

            BelongsToMany::make('Samples'),
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

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
