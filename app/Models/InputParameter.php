<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputParameter extends Model
{
    protected $casts = [
        'parameters' => 'collection'
    ];

    public function assay() {
        return $this->belongsTo(Assay::class);
    }

    public function study() {
        return $this->belongsTo(Study::class);
    }
}
