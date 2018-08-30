<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public function processingLog() {
        return $this->belongsTo(ProcessingLog::class);
    }
}
