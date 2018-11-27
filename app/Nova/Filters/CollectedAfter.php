<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Nova\Filters\DateFilter;

class CollectedAfter extends DateFilter
{
    private $relation;

    public function __construct($relation = null)
    {
        $this->relation = $relation;
    }

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
        if ($this->relation) {
            return $query->whereHas($this->relation, function ($query) use ($value) {
                $query->where('collected_at', '>=', Carbon::parse($value));
            });
        }
        return $query->where('collected_at', '>=', Carbon::parse($value));
    }
}
