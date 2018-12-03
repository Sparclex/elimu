<?php
namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class AcceptedResultDataCollection extends Collection
{
    public function standardDeviationIsInRange($cutoff)
    {
        $filledCqValues = $this->cqValues()->filter();
        return $filledCqValues->count() > 1 ? $filledCqValues->standardDeviation() > $cutoff : true;
    }

    public function hasEnoughValues($minValues)
    {
        return $minValues <= $this->count();
    }

    public function cqValues()
    {
        return $this->map(function ($data) {
            return $data->getOriginal('primary_value');
        });
    }

    public function determineResult($cutoff)
    {
        $cqs = $this->cqValues();

        $isPositive = null;
        $needsRepetition = false;
        foreach ($cqs as $cq) {
            $status = $cq && $cq <= $cutoff ? true : false;
            if ($isPositive === null) {
                $isPositive = $status;
            } elseif ($isPositive !== $status) {
                $needsRepetition = true;
            }
        }
        return $needsRepetition ? -1 : ($isPositive ? 1 : 0);
    }

    public function averageCq()
    {
        return $this->cqValues()->avg();
    }
}
