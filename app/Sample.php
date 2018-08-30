<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'collected_at',
        'received_at',
    ];

    public function deliverer()
    {
        return $this->belongsTo(Person::class, 'deliverer_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Person::class, 'receiver_id');
    }

    public function storage()
    {
        return $this->belongsToMany(Storage::class);
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public function sampleTypes()
    {
        return $this->belongsToMany(SampleType::class, 'storage_places')->withPivot('storage_id')->using(StoragePlace::class);
    }

    public function processingLogs() {
        return $this->hasMany(ProcessingLog::class);
    }
}
