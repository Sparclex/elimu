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

    protected $guarded = [];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }
}
