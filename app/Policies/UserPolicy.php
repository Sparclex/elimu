<?php

namespace App\Policies;

use App\Models\Study;
use App\Providers\AuthServiceProvider;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends Policy
{
    use HandlesAuthorization;

    public function view(User $auth, User $user)
    {
        return true;
    }

    public function create(User $auth)
    {
        return $auth->is_admin;
    }

    public function update(User $auth, User $user)
    {
        return $auth->is($user) || $auth->is_admin;
    }

    public function delete(User $auth, User $user)
    {
        return $auth->is_admin;
    }

    public function restore(User $auth, User $user)
    {
        return $auth->is_admin;
    }

    public function forceDelete(User $auth, User $user)
    {
        return $auth->is_admin;
    }

    public function attachStudy(User $auth, User $user, Study $study)
    {
        return $auth->is_admin || $auth->isManager($study);
    }

    public function detachStudy(User $auth, User $user, Study $study)
    {
        return $auth->is_admin || $auth->isManager($study);
    }
}
