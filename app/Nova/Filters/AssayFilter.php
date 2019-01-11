<?php

namespace App\Nova\Filters;

use App\Models\Assay;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class AssayFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        if (!$assay = Assay::find($value)) {
            return $query;
        }

        $query = $assay->evaluatedResults();
        return $query;
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return Assay::whereHas('results')
            ->pluck('id', 'name');
    }
}
