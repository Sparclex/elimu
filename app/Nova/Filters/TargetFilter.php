<?php

namespace App\Nova\Filters;

use App\Models\Assay;
use App\Models\Result;
use Illuminate\Http\Request;

class TargetFilter extends \AwesomeNova\Filters\DependentFilter
{
    public $dependentOf = [AssayFilter::class];

    public $hideWhenEmpty = true;

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where($query->qualifyColumn('target'), $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request, $filters = [])
    {
        $query =  Result::select('target')->distinct();

        if ($filters[AssayFilter::class]) {
            $query->where('assay_id', $filters[AssayFilter::class]);
        }

        $targets = $query->pluck('target')->toArray();

        return array_combine($targets, $targets);
    }
}
