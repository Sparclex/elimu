<?php

namespace App\Nova\Filters;

use App\Models\Study as StudyModel;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class Study extends Filter
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
        return $query->where('study_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        $studies = StudyModel::orderBy('name')->get(['id', 'study_id', 'name']);
        return $studies->mapWithKeys(function ($study) {
            return [$study->study_id . ": " . $study->name => $study->id];
        });
    }
}
