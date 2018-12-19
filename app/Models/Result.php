<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Result extends Model
{
    use DependsOnStudy;

    protected $fillable = ['sample_id', 'target', 'assay_id'];

    protected $output = null;

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

    public function getStatusAttribute()
    {
        return $this->assay->result_handler::getStatus($this);
    }

    public function getValueAttribute()
    {
        return $this->assay->result_handler::determineResultValue($this);
    }

    public function getInputParameterAttribute()
    {
        return collect($this->assay->inputParameter->parameters)
            ->firstWhere('target', $this->target);
    }

    public function scopeCalculatedResult($query)
    {
        $query->addSubSelect(
            'result_value',
            ResultData::select(
                DB::raw('POW(10, '.$this->inputParameter['slope'].' 
                * AVG(primary_value) 
                + '.$this->inputParameter['intercept'].')')
            )
            ->whereColumn('result_id', 'results.id')
            ->where('status', 1)
            ->groupBy('result_id')
        );
    }
}
