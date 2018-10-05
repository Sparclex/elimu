<?php

namespace App\Policies;

use App\Models\Experiment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExperimentPolicy extends Policy
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
}
