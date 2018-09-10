<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experiment extends Model
{
    public function assay() {
        return $this->belongsTo(Assay::class);
    }

    public function processingLog() {
        return $this->belongsTo(ProcessingLog::class);
    }

    public function samples() {
        return $this->belongsToMany(Sample::class)->withPivot('status');
    }

    public function getStatusAttribute() {
        return optional($this->pivot)->status;
    }

    public function results() {
        return $this->hasMany(Result::class);
    }
}
