<?php

namespace App\Policies;

use App\Models\Experiment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExperimentPolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, Experiment $experiment)
    {
        return $user->study_id == $experiment->study_id;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, Experiment $experiment)
    {
        return true;
    }

    public function delete(User $user, Experiment $experiment)
    {
        return true;
    }

    public function restore(User $user, Experiment $experiment)
    {
        return true;
    }

    public function forceDelete(User $user, Experiment $experiment)
    {
        return true;
    }
}
