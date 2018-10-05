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
        return Authorization::isLabManager($auth);
    }

    public function update(User $auth, User $user)
    {
        return $auth->is($user) || Authorization::isLabManager($auth);
    }

    public function delete(User $auth, User $user)
    {
        return Authorization::isAdministrator($auth);
    }

    public function restore(User $auth, User $user)
    {
        return true;
    }

    public function forceDelete(User $auth, User $user)
    {
        return false;
    }

    public function attachStudy(User $auth, User $user, Study $study)
    {
        return Authorization::isAdministrator($auth);
    }

    public function detachStudy(User $auth, User $user, Study $study)
    {
        return Authorization::isAdministrator($auth);
    }
}
