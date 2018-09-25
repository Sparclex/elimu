<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(User $auth, User $user)
    {
        return $auth->is($user);
    }

    public function create(User $auth)
    {
        return false;
    }

    public function update(User $auth, User $user)
    {
        return $auth->is($user);
    }

    public function delete(User $auth, User $user)
    {
        return false;
    }

    public function restore(User $auth, User $user)
    {
        return true;
    }

    public function forceDelete(User $auth, User $user)
    {
        return false;
    }
}
