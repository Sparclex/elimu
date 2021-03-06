<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Reagent extends Model implements AuditableContract
{
    use Auditable, SetUserStudyOnSave;

    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at'
    ];

    public function assays()
    {
        return $this->belongsToMany(Assay::class);
    }

    public function experiments()
    {
        return $this->hasMany(Experiment::class);
    }
}
