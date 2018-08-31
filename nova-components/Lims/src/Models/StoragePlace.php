<?php

namespace Sparclex\Lims\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StoragePlace extends Pivot
{
    public function getStoredAttribute()
    {
        return (bool) $this->storage_id;
    }

    public function setStoredAttribute($value)
    {
        if ($value === true && ! $this->storage_id) {
            $storage = Storage::generateStorePlace($this->pivotParent->study_id, $this->sample_type_id);
            if($storage) {
                $this->storage_id = $storage->id;
            }
        } elseif ($value === false) {
            $this->storage_id = null;
        }
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function getBoxAttribute() {
        return $this->storage->box;
    }

    public function getFieldAttribute() {
        return $this->storage->field;
    }
}
