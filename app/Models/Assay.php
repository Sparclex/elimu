<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Assay extends Model
{
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
}
