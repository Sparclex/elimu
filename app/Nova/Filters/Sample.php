<?php

namespace App\Nova\Filters;

use App\Models\SampleInformation;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class Sample extends Filter
{
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
        return $query
            ->select('storage.*')
            ->join('samples', 'samples.id', '=', 'sample_id')
            ->where('sample_information_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        return SampleInformation::pluck('id', 'sample_id');
    }
}
