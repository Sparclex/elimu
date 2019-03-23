<?php

namespace App\Experiments\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QPCRStatusFilter extends Filter
{
    protected $name = 'Status';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $status
     * @param array $parameters input parameter for the current target
     * @return \Illuminate\Database\Query\Builder
     */
    public function apply(Builder $query, $status, $parameters)
    {
        $methodName = 'only'.$status;
        if (! method_exists($this, $methodName)) {
            return $query;
        }

        return $this->{$methodName}($query, $parameters);
    }

    public function onlyValid(Builder $query, $parameters)
    {
        return $query->havingRaw(
            '((positives = replicas and stddev <= ?) or (positives = 0))',
            [$parameters['cuttoffstdev']]
        )->having('replicas', $parameters['minvalues']);
    }

    public function onlyErrors(Builder $query, $parameters)
    {
        return $query->havingRaw('positives <> replicas')
            ->having('positives', '>', 0)
            ->orHaving('replicas', '<', $parameters['minvalues'])
            ->orHavingRaw('stddev > ? and positives = replicas', [$parameters['cuttoffstdev']]);
    }

    public function onlyPositive(Builder $query, $parameters)
    {
        return $query->havingRaw('positives = replicas')
            ->having('replicas', '>=', $parameters['minvalues'])
            ->having('stddev', '<=', $parameters['cuttoffstdev']);
    }

    public function onlyNegative(Builder $query, $parameters)
    {
        return $query->having('positives', 0)
            ->having('replicas', '>=', $parameters['minvalues'])
            ->havingRaw('(stddev <= ? or stddev is Null)', [$parameters['cuttoffstdev']]);
    }

    public function onlyMissingReplicates(Builder $query, $parameters)
    {
        return $query->having('replicas', '<', $parameters['minvalues']);
    }

    public function onlyRepetitionNeeded(Builder $query, $parameters)
    {
        return $query->havingRaw('positives <> replicas')
            ->having('positives', '>', 0)
            ->having('replicas', '>=', $parameters['minvalues']);
    }

    public function onlyStddevHigh(Builder $query, $parameters)
    {
        return $query->havingRaw('positives = replicas')
            ->having('replicas', '>=', $parameters['minvalues'])
            ->having('stddev', '>', $parameters['cuttoffstdev']);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Valid' => '',
            'Errors' => '',
            'Positive' => '',
            'Negative' => '',
            'MissingReplicates' => '',
            'RepetitionNeeded' => '',
            'StddevHigh' => '',
        ];
    }
}
