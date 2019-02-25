<?php

namespace App\Models;

use App\Collections\ResultDataCollection;
use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;

class ResultData extends Model
{
    use SetUserStudyOnSave;

    protected $table = 'result_data';

    protected $casts = [
        'extra' => 'array'
    ];

    protected $fillable = ['result_id', 'primary_value', 'secondary_value', 'experiment_id'];

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
