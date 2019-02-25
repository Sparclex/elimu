<?php

namespace App\Models;

use App\Models\Scopes\OnlyCurrentStudy;
use App\Models\Traits\SetUserStudyOnSave;
use App\Observers\ResultExtractor;
use App\User;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Experiment extends Model implements AuditableContract
{
    use SetUserStudyOnSave, Auditable;

    protected $dates = [
        'created_at',
        'updated_at',
        'requested_at',
        'processed_at',
    ];

    protected $dispatchesEvents = [
        'saved' => ResultExtractor::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy);

        static::creating(function ($model) {
            $model->requested_at = now();
        });
    }

    public function assay()
    {
        return $this->belongsTo(Assay::class);
    }

    public function samples()
    {
        return $this
            ->belongsToMany(Sample::class, 'requested_experiments');
    }

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }

    public function resultData()
    {
        return $this->hasMany(ResultData::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class);
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public function getResultHandlerAttribute()
    {
        return $this->assay->result_handler;
    }
}
