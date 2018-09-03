<?php

namespace Sparclex\Lims\Models;

use Illuminate\Database\Eloquent\Model;
use Sparclex\Lims\Observers\AutoStorageSaver;

class Sample extends Model
{
    protected $dispatchesEvents = [
        'updated' => AutoStorageSaver::class,
        'created' => AutoStorageSaver::class,
    ];

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }

    public function sampleInformation()
    {
        return $this->belongsTo(SampleInformation::class);
    }

    public function storage()
    {
        return $this->hasOne(Storage::class);
    }

    public function setStoreAttribute($value)
    {
    }

    public function getStoreAttribute()
    {
        return null;
        //return $this->storage
    }
}
