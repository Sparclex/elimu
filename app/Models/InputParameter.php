<?php

namespace App\Models;

use App\Scopes\OnlyCurrentStudy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InputParameter extends Model
{
    protected $casts = [
        'parameters' => 'collection'
    ];

    protected $fillable = [
        'assay_id', 'study_id', 'parameters'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy('input_parameters'));
    }

    public function assay()
    {
        return $this->belongsTo(Assay::class);
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public static function getByExperiment($experiment)
    {
        if ($experiment instanceof Experiment) {
            $experiment = $experiment->id;
        }
        return optional(self
            ::withoutGlobalScopes()
            ->join('reagents', 'reagents.assay_id', 'input_parameters.assay_id')
            ->join('experiments', 'experiments.reagent_id', 'reagents.id')
            ->where('input_parameters.study_id', Auth::user()->study_id)
            ->where('experiments.id', $experiment)
            ->select('input_parameters.*')->first())->parameters;
    }
}
