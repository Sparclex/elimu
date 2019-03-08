<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Shipment extends Model implements AuditableContract
{
    use SetUserStudyOnSave, Auditable;

    protected $casts = [
        'shipment_date' => 'date'
    ];

    public function samples()
    {
        return $this->belongsToMany(Sample::class, 'shipped_samples')
            ->withPivot('quantity');
    }

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }

    public function recipientPerson()
    {
        return $this->belongsTo(Person::class, 'recipient_person_id');
    }

    public function shipper()
    {
        return $this->belongsTo(Person::class);
    }
}
