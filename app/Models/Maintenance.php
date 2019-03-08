<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $casts = [
        'date' => 'date'
    ];

    public function technician()
    {
        return $this->belongsTo(Person::class);
    }

    public function instrument()
    {
        return $this->belongsTo(Instrument::class);
    }
}
