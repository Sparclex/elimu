<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reagent extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at'
    ];

    public function assay()
    {
        return $this->belongsTo(Assay::class);
    }

    public function experiments()
    {
        return $this->hasMany(Experiment::class);
    }
}
