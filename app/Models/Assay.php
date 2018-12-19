<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Assay extends Model implements AuditableContract
{
    use Auditable;

    public function inputParameter()
    {
        return $this->hasOne(InputParameter::class)
        ->where('study_id', Auth::user()->study_id);
    }

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
        return config('lims.result_types.' . $this->result_type);
    }
}
