<?php

namespace App\Models;

use App\Models\Scopes\OnlyCurrentStudy;
use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Sample extends Model implements AuditableContract
{
    use SetUserStudyOnSave, Auditable;

    protected $dates = [
        'created_at',
        'updated_at',
        'collected_at',
        'birthdate'
    ];

    protected $fillable = [
        'sample_id',
        'subject_id',
        'collected_at',
        'visit_id',
        'study_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy);
    }

    public function sampleTypes()
    {
        return $this->belongsToMany(SampleType::class, 'sample_mutations')
            ->withPivot('quantity');
    }

    public function storagePositions()
    {
        return $this->belongsToMany(SampleType::class, 'storage')
            ->withPivot(['position', 'study_id'])
            ->withTimestamps();
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function experiments()
    {
        return $this->belongsToMany(Experiment::class, 'requested_experiments');
    }

    public function scopeTested(Builder $query, $assay)
    {
        $assayId = $assay instanceof Assay ? $assay->id : $assay;
        return $query->whereHas('results', function ($query) use ($assayId) {
            return $query->where('assay_id', $assayId);
        });
    }

    public function shipments()
    {
        return $this->belongsToMany(Shipment::class, 'shipped_samples')
            ->withPivot('quantity')
            ->orderBy('shipment_date');
    }
}
