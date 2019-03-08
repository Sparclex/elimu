<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SampleType extends Model implements AuditableContract
{
    use Auditable, SetUserStudyOnSave;

    protected $fillable = ['name'];

    public function samples()
    {
        return $this->belongsToMany(Sample::class, 'sample_mutations')
            ->withPivot('quantity')
            ->using(SampleMutation::class);
    }

    public function storages()
    {
        return $this->hasMany(Storage::class);
    }
}
