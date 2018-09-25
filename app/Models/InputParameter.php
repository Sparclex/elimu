<?php

namespace App\Models;

use App\Scopes\OnlyCurrentStudy;
use Illuminate\Database\Eloquent\Model;

class InputParameter extends Model
{
    protected $casts = [
        'parameters' => 'collection'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy);
    }

    public function assay()
    {
        return $this->belongsTo(Assay::class);
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }
}
