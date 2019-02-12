<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AssayDefinitionFile extends Model implements AuditableContract
{
    use Auditable, DependsOnStudy;

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }
}
