<?php

namespace App\Experiments\Qpcr;

class Quantifier
{
    public function quantify(float $cqValue, $slope, $intercept)
    {
        if ($slope === null || $intercept === null) {
            return null;
        }

        return round(
            10 ** ($slope * $this->result->avg_cq + $intercept),
            2
        );
    }
}
