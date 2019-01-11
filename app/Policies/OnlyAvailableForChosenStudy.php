<?php

namespace App\Policies;

use App\User;

trait OnlyAvailableForChosenStudy
{
    public function viewAny(User $user)
    {
        return $user->study_id;
    }

    public function view(User $user, $model)
    {
        return $user->study_id == $model->study_id;
    }

    public function create(User $user)
    {
        return $user->isScientist();
    }

    public function update(User $user, $model)
    {
        return $model->study_id == $user->study_id &&  $user->isScientist();
    }

    public function delete(User $user, $model)
    {
        return $model->study_id == $user->study_id &&  $user->isScientist();
    }
}
