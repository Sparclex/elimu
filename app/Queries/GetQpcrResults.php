<?php

namespace App\Queries;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;

class GetQpcrResults implements AnalizedResultsQuery
{
    /** @var DatabaseManager */
    protected $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    public function run(int $studyId, int $assayId, array $parameters): Builder
    {
        $query = $this->databaseManager
            ->table('samples')
            ->select(
                'samples.sample_id',
                'samples.subject_id',
                'samples.collected_at',
                'samples.visit_id',
                'samples.birthdate',
                'samples.gender'
            );


        foreach ($parameters as $parameter) {
            $normalizedTarget =  preg_replace('/[\W]/', '', $parameter['target']);
            $tableName = $normalizedTarget . '_evaluated_results';
            $query->joinSub(
                $this->results($assayId, $parameter['target'], $parameter['cutoff'] ?? 0),
                $tableName,
                $tableName . '.sample_id',
                'samples.id'
            );

            collect([
                $tableName . '.replicas' => 'replicas_' . $normalizedTarget,
                $tableName . '.avg_cq' => 'mean_cq_' . $normalizedTarget,
                $tableName . '.stddev' => 'sd_cq_' . $normalizedTarget,
                $tableName . '.positives' => 'positives_' . $normalizedTarget,
            ])->each(static function ($alias, $column) use ($query) {
                $query->selectRaw("{$column} as \"{$alias}\"");
            });
        }

        return $query->where('study_id', $studyId);
    }

    private function results(int $assayId, string $target, float $cutoff): Builder
    {
        return $this->databaseManager
            ->table('results')
            ->addSelect('results.sample_id')
            ->selectRaw('count(*) as replicas')
            ->selectRaw('avg(primary_value) as avg_cq')
            ->selectRaw('stddev(primary_value) as stddev')
            ->selectRaw('count(CASE when primary_value <= ? and primary_value <> 0 then 1 end) as positives', [$cutoff])
            ->join('result_data', 'results.id', 'result_data.result_id')
            ->where('included', true)
            ->where('assay_id', $assayId)
            ->where('results.target', $target)
            ->groupBy('result_id');
    }
}
