<?php

namespace App\Models;

use App\Collections\ResultDataCollection;
use Illuminate\Database\Eloquent\Model;

class ResultData extends DependsOnStudy
{
    protected $table = 'result_data';

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }

    public function getAdditionalAttribute($value)
    {
        return unserialize($value);
    }

    public function setAdditionalAttribute($value)
    {
        return serialize($value);
    }


    public function newCollection(array $models = [])
    {
        return new ResultDataCollection($models);
    }
}
