<?php

namespace App\Nova\Lenses;

use App\Models\SampleType;
use App\Models\Storage;
use App\Nova\Filters\SampleFilter;
use App\Nova\Filters\TypeFilter;
use App\Support\Position;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class SampleRegistry extends Lens
{

    /**
     * Apply the specified ordering to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function withOrdering($request, $query)
    {
        if (!$request->orderBy || !$request->orderByDirection) {
            return $query;
        }

        if ($request->lens()->resolveFields($request)->findFieldByAttribute($request->orderBy)) {
            return $query->orderBy(
                $request->orderBy,
                $request->orderByDirection === 'asc' ? 'asc' : 'desc'
            );
        }

        return $query;
    }

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return self::withOrdering(
            $request,
            $request->withFilters(
                $query->select([
                    'samples.*',
                    'sample_mutations.extra as extra',
                    'sample_types.columns',
                    'sample_types.rows',
                    'sample_types.row_format',
                    'sample_types.column_format',
                ])
                    ->join('sample_mutations', 'samples.id', 'sample_mutations.sample_id')
                    ->join('sample_types', 'sample_mutations.sample_type_id', 'sample_types.id')
                    ->join('storage', function ($join) {
                        $join->on('sample_mutations.sample_type_id', 'storage.sample_type_id')
                            ->on('sample_mutations.sample_id', 'storage.sample_id')
                            ->on('storage.study_id', 'samples.study_id');
                    })
                    ->addSubSelect(
                        'type',
                        SampleType::whereColumn('sample_types.id', 'sample_mutations.sample_type_id')
                        ->select('name')
                    )
                    ->addSubSelect(
                        'position',
                        Storage::whereColumn('storage.sample_type_id', 'sample_types.id')
                            ->whereColumn('storage.sample_id', 'samples.id')
                            ->whereColumn('storage.study_id', 'samples.study_id')
                        ->select('position')
                    )
            )
        );
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->hideFromIndex(),
            Text::make('Sample ID')
                ->sortable(),
            Text::make('Extra')->displayUsing(function ($value) {
                return
                    $value->map(function ($data, $key) {
                        return sprintf(
                            '<p class="flex"><strong class="w-1/4">%s</strong><span>%s</span></td>',
                            ucfirst($key),
                            $data
                        );
                    })->implode('');
            })->asHtml(),
            Text::make('Type')->sortable(),
            Text::make('Position')->displayUsing(function ($value) {
                if (!$this->rows || !$this->columns) {
                    return null;
                }
                return Position::fromPosition($value)
                    ->showPlates()
                    ->withRows($this->rows)
                    ->withColumns($this->columns)
                    ->startWithZero()
                    ->withRowFormat($this->row_format)
                    ->withColumnFormat($this->column_format)
                    ->toLabel();
            })->sortable(),

        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new TypeFilter(),
        ];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'sample-registry';
    }
}
