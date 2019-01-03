<?php

namespace App\Models;

use App\Scopes\OnlyCurrentStudy;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Sample extends Model implements AuditableContract
{
    use DependsOnStudy, Auditable;

    protected $fillable = [
        'sample_type_id',
        'sample_information_id',
        'study_id',
        'quantity'
    ];

    protected $casts = [
        'extra' => 'array'
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy);
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

    public function storages()
    {
        return $this->hasMany(Storage::class);
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

    public function shipments()
    {
        return $this->belongsToMany(Shipment::class, 'shipped_samples');
    }
}
