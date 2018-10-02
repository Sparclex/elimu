<?php

namespace App\Nova\Filters;

use App\Models\SampleInformation;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Filters\Filter;

class SampleInformationFilter extends Filter
{
    private $attribute;

    public function __construct($name, $attribute = null)
    {

        $this->name = $name;
        $this->attribute = $attribute ?? str_replace(' ', '_', Str::lower($name));
        ;
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
        return $this->joinSampleInformationsIfNeeded($query)
            ->select('storage.*')
            ->where('sample_informations.' . $this->attribute, $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function joinSampleInformationsIfNeeded($query)
    {
        $joins = $query->getQuery()->joins;
        if ($joins == null) {
            return $this->joinSampleInformations($query);
        }
        foreach ($joins as $join) {
            if ($join->table == 'sample_informations') {
                return $query;
            }
        }
        return $this->joinSampleInformations($query);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function joinSampleInformations($query)
    {
        return $query
            ->join('samples', 'samples.id', '=', 'sample_id')
            ->join('sample_informations', 'sample_informations.id', '=', 'samples.sample_information_id');
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        return SampleInformation::pluck($this->attribute, $this->attribute)->unique();
    }

    public function jsonSerialize()
    {
        $container = Container::getInstance();

        return [
            'class' => get_class($this),
            'name' => $this->name(),
            'options' => collect($this->options($container->make(Request::class)))->map(function ($value, $key) {
                return ['name' => $key, 'value' => $value];
            })->values()->all(),
            'currentValue' => '',
        ];
    }
}
