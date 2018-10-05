<?php

namespace App\Nova;

use App\Actions\ChangeValidationStatus;
use App\Fields\AdditionalData;
use App\Fields\Result;
use App\Fields\Status;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class SampleData extends Resource
{
    use RelationSortable;

    public static $globallySearchable = false;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\SampleData';

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

    public static function singularLabel()
    {
        return 'Sample Data';
    }

    public static function label()
    {
        return 'Sample Data';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable()->onlyOnForms(),
            Text::make('Target')->sortable(),
            BelongsTo::make('Experiment', 'experiment', Experiment::class)->sortable(),
            Text::make('Data', 'primary_value')->sortable(),
            Text::make('Additional Data', 'secondary_value')->sortable(),
            AdditionalData::make('additional'),
            Status::make('Status')
                ->loadingWhen('Pending')
                ->failedWhen('Declined')
                ->successWhen('Accepted')->sortable(),
            Result::make()
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
        return [
            (new ChangeValidationStatus())->canRun(function ($request, $user) {
                return true;
            }),
        ];
    }
    /**
     * Build an "index" query for the given resource.
     *
     * @param  NovaRequest $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return self::sortByMultiple($request, $query, [
            ['experiment', 'id'],
        ]);
    }
}
