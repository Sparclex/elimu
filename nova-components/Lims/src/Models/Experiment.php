<?php

namespace Sparclex\Lims\Models;

use Illuminate\Database\Eloquent\Model;

class Experiment extends Model
{
    public function assay() {
        return $this->belongsTo(Assay::class);
    }

    public function processingLog() {
        return $this->belongsTo(ProcessingLog::class);
    }

    public function sample() {
        return $this->belongsTo(Sample::class);
    }
}
