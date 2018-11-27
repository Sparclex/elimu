<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }

    public function resultData()
    {
        return $this->hasMany(ResultData::class);
    }


    public function getStatusAttribute()
    {
        $allStatusAreSet = $this->resultData->count() ==
            $this->resultData
                ->pluck('status')
                ->filter(function ($value) {
                    return $value !== null;
                })
                ->count();
        return $allStatusAreSet ? 'Verified' : 'Pending';
    }
}
