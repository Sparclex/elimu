<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultData extends Model
{
    protected $table = 'result_data';

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function getDataAttribute($value)
    {
        if ($value) {
            return number_format((float)$value, 2, '.', '\'');
        }
        return 'Non Ampl.';
    }
    public function getAdditionalAttribute($value)
    {
        return unserialize($value);
    }

    public function setAdditionalAttribute($value)
    {
        return serialize($value);
    }
}
