<?php

namespace App\Nova\Filters;

use App\Models\SampleType;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class SampleFilter extends Filter
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
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->whereHas('sampleTypes', function ($query) use ($value) {
            return $query->where('id', $value);
        });
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        return SampleType::orderBy('name')->pluck('id', 'name')->toArray();
    }
}
