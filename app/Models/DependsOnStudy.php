<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

abstract class DependsOnStudy extends Model
{
    protected function preSave()
    {
        if ($this->study_id) {
            return true;
        }

        $this->study_id = Auth::user()->study_id;

        return true;
    }

    public function save(array $options = [])
    {
        if ($this->preSave() === false) {
            return false;
        }
        parent::save($options);
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }
}
