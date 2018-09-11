<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use ZipArchive;

class Data extends Resource
{
    public static $globallySearchable = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Data';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $types = array_keys(config('lims.result_types'));

        return [
            ID::make()->sortable(),
            BelongsTo::make('Experiment')->rules('required', 'exists:experiments,id'),
            Select::make('Type')->options(array_combine($types, $types))->onlyOnForms(),
            File::make('File')->onlyOnForms()->prunable()->store(
                function (Request $request) {
                    $zip = new ZipArchive;
                    $file = $request->file('file');
                    if ($zip->open($file->getRealPath()) !== true) {
                        dump('error');
                        throw new \Exception('Error unzipping');
                    }
                    $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $path = 'app/experiment-data/'.$request->experiment;
                    $zip->extractTo(storage_path($path), [$filename.".xml"]);
                    $zip->close();

                    return ['file' => $path . '/'.$filename.".xml"];
                })->creationRules('required', 'file')->updateRules('file'),
            DateTime::make('Created At')->exceptOnForms(),
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
