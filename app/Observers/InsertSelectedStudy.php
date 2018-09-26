<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class InsertSelectedStudy
{
    public function saving($model)
    {
        if ($model->study_id) {
            return;
        }
        $model->study_id = Auth::user()->study_id;
    }
}
