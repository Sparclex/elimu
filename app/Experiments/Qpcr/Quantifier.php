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
            10 ** ($slope * $cqValue + $intercept),
            2
        );
    }
}
