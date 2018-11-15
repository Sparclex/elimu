<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

trait OnlyViewPolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, $model)
    {
        return $user->study_id == $model->experiment->study_id;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, $model)
    {
        return false;
    }

    public function delete(User $user, $model)
    {
        return false;
    }
}
