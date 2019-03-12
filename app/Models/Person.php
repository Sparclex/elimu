<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = ['name'];

    public function instruments()
    {
        return $this->hasMany(Instrument::class, 'responsible_id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'technician_id');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'shipper_id');
    }
}
