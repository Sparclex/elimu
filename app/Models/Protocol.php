<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Protocol extends Model implements AuditableContract
{
    use Auditable, DependsOnStudy;

    protected $casts = [
        'implemented_at' => 'date'
    ];

    public function responsible()
    {
        return $this->belongsTo(Person::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
