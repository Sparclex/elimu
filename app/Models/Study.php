<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Study extends Model implements AuditableContract
{
    use Auditable;

    public function sampleTypes()
    {
        return $this->belongsToMany(SampleType::class, 'storage_box_sizes')
            ->withPivot(['rows', 'columns']);
    }

    public function samples()
    {
        return $this->hasMany(Sample::class);
    }

    public function assays()
    {
        return $this->hasMany(Assay::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['power']);
    }
}
