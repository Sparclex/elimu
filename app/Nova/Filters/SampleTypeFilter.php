<?php

namespace App\Nova\Filters;

use App\Models\SampleType;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class SampleTypeFilter extends Filter
{
    public $name = 'Sample Type';
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

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
        if (!$value) {
            return $query;
        }
        return $query->where('sample_type_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        return SampleType::pluck('id', 'name');
    }
}
