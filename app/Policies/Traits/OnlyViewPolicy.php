<?php

namespace App\Policies\Traits;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

trait OnlyViewPolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, $model)
    {
        return $user->study_id == $model->study_id;
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
