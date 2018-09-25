<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class InsertSelectedStudy {
    public function saving($model) {
        $model->study_id = Auth::user()->study_id;
    }
}
