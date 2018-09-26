<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SampleType extends Model
{
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
        return $this->belongsToMany(Sample::class);
    }

    public function studies()
    {
        return $this->belongsToMany(Study::class, 'storage_sizes');
    }

    public function getSizeAttribute()
    {
        return optional($this->pivot)->size;
    }
}
