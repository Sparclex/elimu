<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Experiment extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'requested_at',
        'processed_at'
    ];

    public function assay()
    {
        return $this->belongsTo(Assay::class);
    }

    public function samples()
    {
        return $this->belongsToMany(Sample::class, 'experiment_requests');
    }

    public function data()
    {
        return $this->hasMany(Data::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class);
    }

}
