<?php

namespace App\Nova;

use App\CsvToParameter;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class InputParameter extends Resource
{
    use RelationSortable;

    public static $model = 'App\Models\InputParameter';

    public static $title = 'id';

    public static $search = ['id'];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'assay' => ['name'],
    ];

    public static function label()
    {
        return 'Input Parameters';
    }

    public static function singularLabel()
    {
        return 'Input Parameter';
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return self::sortByMultiple($request, $query, [
            ['assay'],
        ]);
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make('Study')
                ->rules('required', 'exists:studies,id')
                ->onlyOnDetail(),
            BelongsTo::make('Assay')
                ->rules('required', 'exists:assays,id')
                ->sortable(),
            Text::make('Name')
                ->rules('nullable')
                ->sortable(),
            Code::make('Parameters')
                ->json()
                ->onlyOnDetail(),
            File::make('Parameter File')
                ->rules('required', 'file')
                ->disk('local')
                ->store(new CsvToParameter)
                ->prunable()
                ->download(function ($request, $model) {
                    return \Illuminate\Support\Facades\Storage::disk('local')
                        ->download($model->parameter_file, $model->name . " Parameters.csv");
                })
        ];
    }
}
