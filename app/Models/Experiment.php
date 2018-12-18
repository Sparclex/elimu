<?php

namespace App\Models;

use App\Observers\ExtractSampleData;
use App\ResultHandlers\ResultHandler;
use App\Scopes\OnlyCurrentStudy;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Experiment extends DependsOnStudy
{
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
