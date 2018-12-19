<?php

namespace App\Models;

use App\Collections\ResultDataCollection;
use Illuminate\Database\Eloquent\Model;

class ResultData extends Model
{
    use DependsOnStudy;

    protected $table = 'result_data';

    protected $casts = [
        'extra' => 'array'
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }


    public function newCollection(array $models = [])
    {
        return new ResultDataCollection($models);
    }
}
