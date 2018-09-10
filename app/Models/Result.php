<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }

    public function getResultAttribute()
    {
        if (! $this->type) {
            return [];
        }
        $handler = config('lims.result_types')[$this->type];

        return [
            'type' => $this->type,
            'data' => (new $handler($this->file, []))->handle(),
        ];
    }
}
