<?php

namespace App\Nova;

use App\Fields\CustomBelongsToMany;
use App\Fields\SampleIds;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Treestoneit\BelongsToField\BelongsToField;

class Experiment extends Resource
{
    use RelationSortable;

    public static $model = 'App\Models\Experiment';

    public static $search = ['id', 'name'];

    public static $globallySearchable = false;

    public static function indexQuery(NovaRequest $request, $query)
    {
        return self::sortBy($request, $query->withCount('samples'), 'assay');
    }

    public function title()
    {
        return sprintf('%d %s', $this->id, $this->name);
    }

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            SampleIds::make('Samples')
                ->help('A new line for each sample id')
                ->rules('required'),
            Text::make('Name')
                ->sortable(),
            BelongsToField::make('Assay')
                ->sortable(),
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

            Text::make('Original Filename')
                ->onlyOnDetail(),

            File::make('Result File')
                ->hideWhenCreating()
                ->disk('local')
                ->path('experiments')
                ->prunable()
                ->storeOriginalName('original_filename')
                ->deletable(false),

            HasMany::make('Data', 'resultData', ResultData::class)
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            (new DownloadExcel)->withHeadings()->allFields(),
        ];
    }
}
