<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends DependsOnStudy
{
    protected $fillable = ['sample_id', 'target', 'assay_id'];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function assay()
    {
        return $this->belongsTo(Assay::class);
    }

    public function resultData()
    {
        return $this->hasMany(ResultData::class);
    }
}
