<?php

namespace App\Policies;

use App\Models\SampleType;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SampleTypePolicy extends Policy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, $model)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isScientist();
    }

    public function update(User $user, $model)
    {
        return $user->isScientist();
    }

    public function delete(User $user, $model)
    {
        return $user->is_admin;
    }
}
