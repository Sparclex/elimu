<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SampleMutation extends Pivot
{
    protected $table = 'sample_mutations';

    protected $casts = [
        'extra' => 'collection'
    ];
}
