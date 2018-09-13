<?php

namespace App\Models;

use App\Observers\ExtractSampleData;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{

    protected $casts = [
        'additional' => 'array'
    ];

    protected $dispatchesEvents = [
        'saved' => ExtractSampleData::class,
    ];
    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }

    public function samples() {
        return $this->belongsToMany(Sample::class)->withPivot(['status', 'target']);
    }

    public function getStatusAttribute() {
        return optional($this->pivot)->status;
    }
}
