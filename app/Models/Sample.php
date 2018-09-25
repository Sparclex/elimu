<?php

namespace App\Models;

use App\Scopes\OnlyCurrentStudy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Sample extends Model
{
    protected $fillable = ['sample_type_id', 'sample_information_id', 'study_id', 'quantity'];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy('sampleInformation'));
    }

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

    public function experiments()
    {
        return $this->belongsToMany(Experiment::class, 'experiment_requests')->withTimestamps();
    }

    public function data()
    {
        return $this->hasMany(SampleData::class);
    }
}
