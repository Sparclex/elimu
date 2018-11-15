<?php

namespace App\Policies;

use App\Models\Study;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudyPolicy extends Policy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return Authorization::isLabManager($user);
    }

    public function view(User $user, Study $study)
    {
        return $user->studies->contains($study)
        || Authorization::isAdministrator($user);
    }

    public function create(User $user)
    {
        return Authorization::isAdministrator($user);
    }

    public function update(User $user, Study $study)
    {
        return ($user->studies->contains($study) && Authorization::isLabManager($user))
        || Authorization::isAdministrator($user);
    }

    public function delete(User $user, Study $study)
    {
        return Authorization::isAdministrator($user);
    }
}
