<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SampleType extends Model
{
    public static function boot()
    {
        parent::boot();
        Pivot::creating(function(Pivot $pivot) {
            if($pivot->getTable() == 'storage_places') {
                $pivot->stores = true;
            }
        });
    }

    public function storageSizes() {
        return $this->hasMany(StorageSize::class);
    }

    public function samples() {
        return $this->belongsToMany(Sample::class);
    }
}
