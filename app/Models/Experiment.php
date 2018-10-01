<?php

namespace App\Models;

use App\Observers\ExtractSampleData;
use App\Scopes\OnlyCurrentStudy;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Experiment extends Model
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
        return $this->belongsToMany(Sample::class, 'experiment_requests');
    }

    public function data()
    {
        return $this->belongsToMany(Sample::class, 'data_sample')->withPivot(['status', 'target']);
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
        return InputParameter
            ::withoutGlobalScopes()
            ->join('reagents', 'reagents.assay_id', 'input_parameters.assay_id')
            ->join('experiments', 'experiments.reagent_id', 'reagents.id')
            ->where('input_parameters.study_id', Auth::user()->study_id)
            ->where('experiments.id', $this->id)
            ->select('input_parameters.*')->first()->parameters;
    }
}
