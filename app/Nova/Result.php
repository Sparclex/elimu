<?php

namespace App\Nova;

use App\Models\Assay;
use App\Nova\Filters\AssayFilter;
use App\Nova\Filters\ResultFilter;
use App\Nova\Filters\TargetFilter;
use App\Support\QPCRResultSpecifier;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\TrashedStatus;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Treestoneit\BelongsToField\BelongsToField;

class Result extends Resource
{
    public static $displayInNavigation = false;

    public static $model = 'App\Models\Result';

    public static $title = 'id';

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
            (new DownloadExcel())->withHeadings(),
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

        return static::indexQuery($request, $query);
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if (! count($query->getQuery()->getRawBindings()['where'])) {
            return $query;
        }

        $assay = Assay::where('id', $query->getQuery()->getRawBindings()['where'][0])->first();

        if (! $assay) {
            return $query;
        }

        return $assay->definitionFile->resultTypeClass()::indexQuery($query, $assay);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return boolean
     */
    protected function assayFilterIsset(Request $request)
    {
        return ! ! optional(
            collect(json_decode(base64_decode($request->get('filters'))))
            ->firstWhere('class', AssayFilter::class)
        )->value;
    }
}
