<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }
}
