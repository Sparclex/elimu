<?php
namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class ResultDataCollection extends Collection
{
    public function onlyAccepted()
    {
        return new AcceptedResultDataCollection($this->where('status', 1));
    }
}
