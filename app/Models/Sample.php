<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\AutoStorageSaver;

class Sample extends Model
{
    protected $fillable = ['sample_type_id', 'sample_information_id', 'study_id', 'quantity'];

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

    public function experiments() {
        return $this->belongsToMany(Experiment::class)->withPivot('status')->withTimestamps();
    }

    public function getStatusAttribute() {
        return optional($this->pivot)->status;
    }
}
