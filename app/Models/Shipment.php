<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Shipment extends Model implements AuditableContract
{
    use DependsOnStudy, Auditable;

    protected $casts = [
        'shipment_date' => 'date'
    ];

    public function samples()
    {
        return $this->belongsToMany(Sample::class, 'shipped_samples')
            ->withPivot('quantity');
    }
}
