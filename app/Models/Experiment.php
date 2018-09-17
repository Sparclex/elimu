<?php

namespace App\Models;

use App\Observers\ExtractSampleData;
use App\User;
use Illuminate\Database\Eloquent\Model;

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

    public function assay()
    {
        return $this->belongsTo(Assay::class);
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

    public function getInputParametersAttribute() {
         return optional($this->join('assays', 'assays.id', '=', 'experiments.assay_id')
            ->join('input_parameters', 'input_parameters.assay_id', '=', 'assays.id')
            ->select('input_parameters.*')->first())->parameters;
    }
}
