<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    public function storageSizes() {
        return $this->hasMany(StorageSize::class);
    }

    public function samples() {
        return $this->hasMany(Sample::class);
    }
}
