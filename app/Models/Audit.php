<?php

namespace App\Models;

use App\Models\Scopes\OnlyCurrentStudy;
use App\Models\Traits\SetUserStudyOnSave;
use OwenIt\Auditing\Models\Audit as AuditModel;

class Audit extends AuditModel
{
    use SetUserStudyOnSave;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy);
    }
}
