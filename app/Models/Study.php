<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Study extends Model
{

    public function sampleInformations()
    {
        return $this->hasMany(SampleInformation::class);
    }

    public function sampleTypes()
    {
        return $this->belongsToMany(SampleType::class, 'storage_sizes')->withPivot('size');
    }
}
