<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Study extends Model implements AuditableContract
{
    use Auditable;

    public function sampleInformations()
    {
        return $this->hasMany(SampleInformation::class);
    }

    public function sampleTypes()
    {
        return $this->belongsToMany(SampleType::class, 'storage_sizes')->withPivot('size');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
