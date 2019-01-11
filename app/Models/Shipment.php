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
        return $this->belongsToMany(SampleMutation::class, 'shipped_samples');
    }

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }
}
