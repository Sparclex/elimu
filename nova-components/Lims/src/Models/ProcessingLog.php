<?php

namespace Sparclex\Lims\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingLog extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'processed_at'
    ];

    public function deliverer() {
        return $this->belongsTo(Person::class);
    }

    public function receiver() {
        return $this->belongsTo(Person::class);
    }

    public function collector() {
        return $this->belongsTo(Person::class);
    }

    public function results() {
        return $this->hasMany(Result::class);
    }

    public function experiment() {
        return $this->hasMany(Experiment::class);
    }
}
