<?php

namespace App\Policies;

use App\Policies\Traits\OnlyAvailableForChosenStudy;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssayPolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function delete(User $user, $model)
    {
        return $user->isManager();
    }

    public function view(User $user, $model)
    {
        return $user->study_id != null;
    }

    public function create(User $user)
    {
        return $user->isScientist();
    }

    public function update(User $user, $model)
    {
        return $user->isScientist();
    }
}
