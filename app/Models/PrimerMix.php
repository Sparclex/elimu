<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrimerMix extends Model
{
    protected $casts = [
        'expires_at' => 'date'
    ];

    public function setExpiresInAttribute($value)
    {
        $this->expires_at = now()->addDays($value);
    }

    public function getExpiresInAttribute()
    {
        return now()->diffInDays($this->expires_at);
    }

    public function creator()
    {
        return $this->belongsTo(Person::class);
    }

    public function reagent()
    {
        return $this->belongsTo(Reagent::class);
    }
}
