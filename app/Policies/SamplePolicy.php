<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SamplePolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, $model)
    {
        return $user->study_id == $model->sampleInformation->study_id;
    }

    public function create(User $user)
    {
        return Authorization::isScientist($user);
    }

    public function update(User $user, $model)
    {
        return $user->study_id == $model->sampleInformation->study_id
            && Authorization::isScientist();
    }

    public function delete(User $user, $model)
    {
        return $user->study_id == $model->sampleInformation->study_id
            && $this->createdFiveMinutesAgo($user, $model);
    }
}
