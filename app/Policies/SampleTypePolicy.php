<?php

namespace App\Policies;

use App\Models\SampleType;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SampleTypePolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function delete(User $user, $model)
    {
        return Authorization::isAdministrator($user);
    }

    public function view(User $user, $model)
    {
        return true;
    }

    public function create(User $user)
    {
        return Authorization::isScientist($user);
    }

    public function update(User $user, $model)
    {
        return Authorization::isScientist($user);
    }
}
