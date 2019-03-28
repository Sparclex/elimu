<?php

namespace App\Nova;

use App\Actions\ResultExport;
use App\Models\Assay;
use App\Nova\Filters\AssayFilter;
use App\Nova\Filters\ResultFilter;
use App\Nova\Filters\TargetFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\TrashedStatus;
use Treestoneit\BelongsToField\BelongsToField;

class Result extends Resource
{
    public static $displayInNavigation = false;

    public static $model = 'App\Models\Result';

    public static $title = 'id';

    public static $searchRelations = [
        'sample' => ['sample_id'],
    ];

    public static $search = [];

    public function fields(Request $request)
    {
        $fields = [
            ID::make()
                ->hideFromIndex(),
            BelongsToField::make('Sample'),
            BelongsToField::make('Assay'),
            Text::make('Target')->sortable(),
            Text::make(
                'Type',
                function () {
                    if (! $this->assay) {
                        return null;
                    }

                    return $this->assay
                        ->definitionFile
                        ->sampleType
                        ->name;
                }
            ),
            Number::make('Replicates', 'replicas')->sortable(),
            HasMany::make('Data', 'resultData', ResultData::class),
        ];

        if ($this->assayFilterIsset($request) && $this->assay) {
            $fields = array_merge(
                $fields,
                $this->assay->definitionFile->resultTypeClass()::fields($request, $this)
            );
        }

        return $fields;
    }

    public function filters(Request $request)
    {
        return [
            new AssayFilter,
            new TargetFilter,
            new ResultFilter,
        ];
    }

    public function actions(Request $request)
    {
        return [
            (new ResultExport())
        ];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $search
     * @param  array $filters
     * @param  array $orderings
     * @param  string $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildIndexQuery(
        NovaRequest $request,
        $query,
        $search = null,
        array $filters = [],
        array $orderings = [],
        $withTrashed = TrashedStatus::DEFAULT
    ) {
        $query = static::applyOrderings(
            static::applyFilters(
                $request,
                $query,
                $filters
            ),
            $orderings
        );
        if ($search && ! empty(trim($search))) {
            $query = static::applySearch($query, $search);
        }

        return static::indexQuery($request, $query, $filters);
    }

    public static function indexQuery(NovaRequest $request, $query, $filters = null)
    {
        $assay = null;

        $assayFilter = collect($filters)->first(
            function ($filter) {
                return $filter->class = AssayFilter::class;
            }
        );

        if ($request->get('viaResource') == 'assays') {
            $assay = Assay::where('id', $request->get('viaResourceId'))->first();
        } elseif ($assayFilter && $assayFilter->value) {
            $assay = Assay::where('id', $assayFilter->value)->first();
        }

        if (! $assay) {
            return $query->addSubSelect('replicas', \App\Models\ResultData::selectRaw('count(*)')
                ->whereColumn('result_data.result_id', 'results.id')
                ->where('included', true));
        }

        return $assay->definitionFile->resultTypeClass()::indexQuery($query, $assay);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return boolean
     */
    protected function assayFilterIsset(Request $request)
    {
        return (optional(
            collect(json_decode(base64_decode($request->get('filters'))))
                    ->firstWhere('class', AssayFilter::class)
        )->value || $request->get('viaResource') == 'assays');
    }
}
