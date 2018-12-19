<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Reagent extends Model implements AuditableContract
{
    use Auditable;

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
