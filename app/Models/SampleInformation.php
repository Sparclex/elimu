<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleInformation extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'collected_at'
    ];

    protected $fillable = [
        'sample_id', 'subject_id', 'collected_at', 'visit_id'
    ];
}
