<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Control extends Model implements AuditableContract
{
    use Auditable, SetUserStudyOnSave;

    public function assays()
    {
        return $this->belongsToMany(Assay::class);
    }
}
