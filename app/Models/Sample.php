<?php

namespace App\Models;

use App\Scopes\OnlyCurrentStudy;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $with = ['sampleInformation'];

    protected $fillable = ['sample_type_id', 'sample_information_id', 'study_id', 'quantity'];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy('sampleInformation', true));
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
        return $this
            ->belongsToMany(Experiment::class, 'requested_experiments')
            ->withTimestamps();
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function scopeWithType($query)
    {
        $query->addSubSelect('sample_type_name', SampleType::select('name')
                            ->whereColumn('id', 'sample_type_id'));
    }

    public function scopeWithSampleId($query)
    {
        $query->addSubSelect('sample_id', SampleInformation::select('sample_id')
        ->whereColumn('id', 'sample_information_id'));
    }
}
