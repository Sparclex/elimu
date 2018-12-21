<?php

namespace App\Models;

use App\Scopes\OnlyCurrentStudy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Models\Audit as AuditModel;

class Audit extends AuditModel
{
    use DependsOnStudy;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy);
    }
}
