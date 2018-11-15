<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User;

class ResultDataPolicy
{
    use OnlyViewPolicy;

    public function view(User $user, $model)
    {
        return $user->study_id == $model->result->experiment->study_id;
    }
}
