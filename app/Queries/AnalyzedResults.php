<?php

namespace App\Queries;

use App\Models\Assay;
use App\Models\ResultData;
use App\Models\Sample;
use Illuminate\Support\Facades\DB;

class AnalyzedResults
{
    public function query(Assay $assay)
    {
        $query = Sample::query();
        foreach ($assay->parameters as $parameter) {
            $target = $parameter['target'];
            $query->join(
                'results as ' . $target . '_results',
                $target . '_results.sample_id',
                'samples.id'
            )
                ->where($target . '_results.assay_id', $assay->id)
                ->where($target . '_results.target', $target)
                ->whereExists(function ($query) use ($target) {
                    $query->selectRaw('1')
                        ->from('result_data as '. $target. '_has_results')
                        ->where($target. '_has_results.status', 1)
                        ->whereColumn($target. '_has_results.id', $target . '_results.id');
                });

            $query = $this->addCombinedResultData(
                $query,
                $target,
                $parameter['cutoff'],
                $parameter['slope'],
                $parameter['intercept']
            );
        }

        return $query;
    }

    public function get(Assay $assay)
    {
        return $this->query($assay)->get();
    }

    private function addCombinedResultData($query, $target, $cutoff, $slope, $intercept)
    {
        $query->joinSub(
            ResultData::selectRaw('result_id,
                COUNT(*) as accepted,
                COUNT(primary_value) as accepted_not_null,
                AVG(primary_value) as mean,
                STDDEV_SAMP(primary_value) as standard_dev'
                . $this->selectQuantitativeColumn($slope, $intercept))
                ->groupBy('result_id'),
            $target,
            function ($join) use ($target) {
                $join->on($target.'_results.id', $target . '.result_id');
            }
        )
            ->addSubSelect(
                $target . '_positive_count',
                $this->countPositiveResultData($target, $cutoff)
            )
            ->addSubSelect(
                $target . '_negative_count',
                $this->countNegativeResultData($target, $cutoff)
            )
            ->addSelect([
                $target . '.accepted as ' . $target . '_accepted',
                $target . '.mean as ' . $target . '_mean',
                $target . '.standard_dev as ' . $target . '_stddev',
                $target . '.quantitative as ' . $target . '_quantitative',
            ]);

        return $query;
    }

    private function selectQuantitativeColumn($slope, $intercept)
    {
        $selectStatement = ', ';
        if ($slope) {
            $selectStatement .= 'POW(10, ' . $slope .
                ' * AVG(primary_value)
                + ' . $intercept . ')';
        } else {
            $selectStatement .= 'NULL';
        }

        return $selectStatement . ' as quantitative';
    }

    private function countPositiveResultData($target, $cutoff)
    {
        return ResultData::selectRaw('count(*)')
            ->where('primary_value', '<=', $cutoff)
            ->whereColumn('result_id', $target.'_results.id');
    }

    private function countNegativeResultData($target, $cutoff)
    {
        return ResultData::selectRaw('count(*)')
            ->where(function ($query) use ($cutoff) {
                return $query->where('primary_value', '>', $cutoff)
                    ->orWhereNull('primary_value');
            })
            ->whereColumn('result_id', $target . '_results.id');
    }
}
