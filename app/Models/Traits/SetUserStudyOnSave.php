<?php

namespace App\Models\Traits;

use App\Models\Scopes\OnlyCurrentStudy;
use App\Models\Study;
use Illuminate\Support\Facades\Auth;

trait SetUserStudyOnSave
{
    public static function bootSetUserStudyOnSave()
    {
        static::addGlobalScope(new OnlyCurrentStudy());
    }

    public function save(array $options = [])
    {
        if ($this->preSave() === false) {
            return false;
        }
        parent::save($options);
    }

    protected function preSave()
    {
        if ($this->study_id) {
            return true;
        }

        $this->study_id = Auth::user()->study_id;

        return true;
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }
}
