<?php

namespace Sparclex\Lims\Models;

use Illuminate\Database\Eloquent\Model;

class SampleInformation extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'date'
    ];
}
