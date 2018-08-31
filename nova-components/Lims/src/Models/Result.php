<?php

namespace Sparclex\Lims\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public function processingLog() {
        return $this->belongsTo(ProcessingLog::class);
    }
}
