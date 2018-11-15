<?php

namespace App\Collections;

use Illuminate\Support\Collection;

class RdmlParameterCollection extends Collection
{
    public function thresholds()
    {
        return $this->pluck('threshold', 'target');
    }
}
