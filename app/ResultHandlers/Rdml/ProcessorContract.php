<?php

namespace App\ResultHandlers\Rdml;

interface ProcessorContract
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function cyclesOfQuantification();
}
