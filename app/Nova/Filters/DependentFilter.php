<?php

namespace App\Nova\Filters;

use App\Models\Assay;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravel\Nova\Filters\Filter;

abstract class DependentFilter extends Filter
{
    public $dependsOn = [];

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'filters-dependent-select';

    public function dependsOn()
    {
        return $this->dependsOn;
    }
    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        $dependencies = collect(json_decode(base64_decode($request->get('filters'))))
            ->filter(
                function ($filter) {
                    return in_array($filter->class, $this->dependsOn())
                        || (array_key_exists($filter->class, $this->dependsOn()) &&
                            in_array($filter->value, $this->dependsOn()[$filter->class]));
                }
            );

        if ($dependencies->count() < count($this->dependsOn())) {
            return $query;
        }

        return $this->applyWithDependencies($request, $query, $value, $dependencies->pluck('value'));

        $assayFilter = collect(json_decode(base64_decode($request->get('filters'))))
            ->firstWhere('class', AssayFilter::class);

        if (! $assayFilter || ! ($assay = Assay::where('id', $assayFilter->value)->first())) {
            return $query;
        }

        $methodName = 'only'.$value;
        if (! method_exists($this, $methodName)) {
            return $query;
        }

        return $this->{$methodName}($query, $assay->definitionFile->parameters);
    }

    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @param Collection $dependencies
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyWithDependencies(Request $request, $query, $value, $dependencies)
    {
        return $query;
    }

    public function jsonSerialize()
    {
        $this->withMeta(['dependsOn' => $this->dependsOn()]);

        return parent::jsonSerialize();
    }

    public function getOptions(Request $request, array $filters = [])
    {
        return collect(
            $this->options($request, $filters)
        )->map(function ($key, $value) {
            return is_array($value) ? ($value + ['value' => $key]) : ['label' => $value, 'value' => $key];
        })->values()->all();
    }

    public function options(Request $request, $filters = [])
    {
    }
}
