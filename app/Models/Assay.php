<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assay extends Model
{
    public function inputParameters() {
        return $this->hasOne(InputParameter::class);
    }
}
