<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SampleType extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = ['name'];

    public static function boot()
    {
        parent::boot();
        Pivot::creating(function (Pivot $pivot) {
            if ($pivot->getTable() == 'storage_places') {
                $pivot->stores = true;
            }
        });
    }

    public function samples()
    {
        return $this->belongsToMany(Sample::class, 'sample_mutations');
    }

    public function studies()
    {
        return $this->belongsToMany(Study::class, 'storage_box_sizes')
            ->withPivot(['rows', 'columns']);
    }

    public function getSizeAttribute()
    {
        return optional($this->pivot)->size;
    }

    public function storages()
    {
        return $this->hasMany(Storage::class);
    }
}
