<?php

namespace App\Nova\Filters;

use App\Models\Assay;
use Illuminate\Http\Request;

class ResultFilter extends DependentFilter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'filters-dependent-select';

    public function dependsOn()
    {
        return [
            AssayFilter::class => Assay::pluck('id')->toArray(),
        ];
    }

    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @param \Illuminate\Support\Collection $dependencies
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyWithDependencies(Request $request, $query, $value, $dependencies)
    {
        $methodName = 'only'.$value;

        if (! method_exists($this, $methodName) || ! ($assay = Assay::where('id', $dependencies->first())->first())) {
            return $query;
        }

        return $this->{$methodName}($query, $assay->definitionFile->parameters);
    }

    public function onlyValid($query, $parameters)
    {
        $sql = $parameters->map(
            function ($targetParameters) {
                return [
                    'sql' => '(
                    (
                        replicas >= ? and
                        (
                            (positives = replicas and stddev <= ?) 
                            or positives = 0
                        )
                    ) and results.target = ?)',
                    'bindings' => [
                        $targetParameters['minvalues'],
                        $targetParameters['cuttoffstdev'],
                        $targetParameters['target'],
                    ],
                ];
            }
        );

        return $query->havingRaw(
            sprintf('(%s)', $sql->pluck('sql')->implode(' or ')),
            $sql->pluck('bindings')->flatten()->toArray()
        );
    }

    public function onlyErrors($query, $parameters)
    {
        $sql = $parameters->map(
            function ($targetParameters) {
                return [
                    'sql' => '(
                    (
                        replicas < ? 
                        or (stddev > ? and positives = replicas)
                    )  and results.target = ?)',
                    'bindings' => [
                        $targetParameters['minvalues'],
                        $targetParameters['cuttoffstdev'],
                        $targetParameters['target'],
                    ],
                ];
            }
        );

        return $query->havingRaw('(positives <> replicas and positives > 0)')
            ->orHavingRaw(
                sprintf('(%s)', $sql->pluck('sql')->implode(' or ')),
                $sql->pluck('bindings')->flatten()->toArray()
            );
    }

    public function onlyPositive($query, $parameters)
    {
        $sql = $parameters->map(
            function ($targetParameters) {
                return [
                    'sql' => '(replicas >= ? and stddev <= ? and results.target = ?)',
                    'bindings' => [
                        $targetParameters['minvalues'],
                        $targetParameters['cuttoffstdev'],
                        $targetParameters['target'],
                    ],
                ];
            }
        );

        return $query->havingRaw('positives = replicas')
            ->havingRaw(
                sprintf('(%s)', $sql->pluck('sql')->implode(' or ')),
                $sql->pluck('bindings')->flatten()->toArray()
            );
    }

    public function onlyNegative($query, $parameters)
    {
        $sql = $parameters->map(
            function ($targetParameters) {
                return [
                    'sql' => '(replicas >= ? and stddev <= ? or stddev is Null and results.target = ?)',
                    'bindings' => [
                        $targetParameters['minvalues'],
                        $targetParameters['cuttoffstdev'],
                        $targetParameters['target'],
                    ],
                ];
            }
        );

        return $query->having('positives', 0)
            ->havingRaw(
                sprintf('(%s)', $sql->pluck('sql')->implode(' or ')),
                $sql->pluck('bindings')->flatten()->toArray()
            );
    }

    public function onlyMissingReplicates($query, $parameters)
    {
        $sql = $parameters->map(
            function ($targetParameters) {
                return [
                    'sql' => '(replicas < ? and results.target = ?)',
                    'bindings' => [
                        $targetParameters['minvalues'],
                        $targetParameters['target'],
                    ],
                ];
            }
        );

        return $query->havingRaw(
            sprintf('(%s)', $sql->pluck('sql')->implode(' or ')),
            $sql->pluck('bindings')->flatten()->toArray()
        );
    }

    public function onlyRepetitionNeeded($query, $parameters)
    {
        $sql = $parameters->map(
            function ($targetParameters) {
                return [
                    'sql' => '(replicas >= ? and results.target = ?)',
                    'bindings' => [
                        $targetParameters['minvalues'],
                        $targetParameters['target'],
                    ],
                ];
            }
        );

        return $query->havingRaw('positives <> replicas')
            ->having('positives', '>', 0)
            ->havingRaw(
                sprintf('(%s)', $sql->pluck('sql')->implode(' or ')),
                $sql->pluck('bindings')->flatten()->toArray()
            );
    }

    public function onlyStddevHigh($query, $parameters)
    {
        $sql = $parameters->map(
            function ($targetParameters) {
                return [
                    'sql' => '(replicas >= ? and stddev > ? and results.target = ?)',
                    'bindings' => [
                        $targetParameters['minvalues'],
                        $targetParameters['cuttoffstdev'],
                        $targetParameters['target'],
                    ],
                ];
            }
        );

        return $query->havingRaw('positives = replicas')
            ->havingRaw(
                sprintf('(%s)', $sql->pluck('sql')->implode(' or ')),
                $sql->pluck('bindings')->flatten()->toArray()
            );
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @param array $filters
     * @return array
     */
    public function options(Request $request, $filters = [])
    {
        return array_flip(
            [
                'Valid' => 'Valid',
                'Errors' => 'Errors',
                'Positive' => 'Positive',
                'Negative' => 'Negative',
                'MissingReplicates' => 'Not enough repetitions',
                'RepetitionNeeded' => 'Inconsistent Results',
                'StddevHigh' => 'Standard deviation too high',
            ]
        );
    }
}
