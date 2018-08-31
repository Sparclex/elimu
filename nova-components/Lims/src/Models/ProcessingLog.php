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

    public function sample() {
        return $this->belongsTo(Sample::class);
    }

    public function test() {
        return $this->belongsTo(Test::class);
    }

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
}
