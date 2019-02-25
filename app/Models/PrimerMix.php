<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PrimerMix extends Model implements AuditableContract
{
    use Auditable, SetUserStudyOnSave;

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
