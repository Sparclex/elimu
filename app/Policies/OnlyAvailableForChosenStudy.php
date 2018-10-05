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
        return Authorization::isScientist($user);
    }

    public function update(User $user, $model)
    {
        return $user->study_id == $model->study_id
            && Authorization::isScientist();
    }

    public function delete(User $user, $model)
    {
        return $user->study_id == $model->study_id
             && $this->createdFiveMinutesAgo($user, $model);
    }
}
