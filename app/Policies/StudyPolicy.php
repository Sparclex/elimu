<?php

namespace App\Policies;

use App\Models\Study;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudyPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Study $study)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Study $study)
    {
        return true;
    }

    public function delete(User $user, Study $study)
    {
        return true;
    }

    public function restore(User $user, Study $study)
    {
        return true;
    }

    public function forceDelete(User $user, Study $study)
    {
        return true;
    }
}
