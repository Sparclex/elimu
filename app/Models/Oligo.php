<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;

class Oligo extends Model
{
    use SetUserStudyOnSave;

    public function assays()
    {
        return $this->belongsToMany(Assay::class)
            ->withPivot('concentration');
    }
}
