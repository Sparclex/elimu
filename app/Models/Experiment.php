<?php

namespace App\Models;

use App\User;
use OwenIt\Auditing\Auditable;
use App\Scopes\OnlyCurrentStudy;
use App\Observers\ExtractSampleData;
use Illuminate\Support\Facades\Auth;
use App\ResultHandlers\ResultHandler;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Experiment extends Model implements AuditableContract
{
    use DependsOnStudy, Auditable;

    protected $dates = [
        'created_at',
        'updated_at',
        'requested_at',
        'processed_at',
    ];

    protected $dispatchesEvents = [
        'saved' => ExtractSampleData::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy('experiments'));

        static::saving(function ($model) {
            $model->requested_at = now();
            $model->requester_id = Auth::user()->id;
        });
    }

    public function reagent()
    {
        return $this->belongsTo(Reagent::class);
    }

    public function samples()
    {
        return $this
            ->belongsToMany(Sample::class, 'requested_experiments')
            ->withTimestamps();
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

    public function getInputParametersAttribute()
    {
        return InputParameter::getByExperiment($this->id);
    }

    public function getResultHandlerAttribute()
    {
        return $this->reagent->assay->result_handler;
    }

    public function scopeWithAssayName($query)
    {
        return $query->addSubSelect('assay_id', Reagent::select('assay_id')
            ->whereColumn('id', 'reagent_id'))
            ->addSubSelect('assay_name', Assay::select('name')
                ->whereColumn('id', 'assay_id'));
    }

    public function scopeWithRequesterName($query)
    {
        return $query->addSubSelect('requester_name', User::select('name')
            ->whereColumn('id', 'requester_id'));
    }
}
