<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Assay extends Model implements AuditableContract
{
    use Auditable;

    protected $casts = [
        'parameters' => 'collection'
    ];

    public function reagents()
    {
        return $this->hasMany(Reagent::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function getResultHandlerAttribute()
    {
        return config('lims . result_types . ' . $this->result_type);
    }
}
