<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Instrument extends Model implements AuditableContract
{
    use Auditable;

    public function responsible()
    {
        return $this->belongsTo(Person::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
