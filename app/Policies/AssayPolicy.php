<?php

namespace App\Policies;

use App\Models\Assay;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssayPolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, Assay $assay)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Assay $assay)
    {
        return false;
    }

    public function delete(User $user, Assay $assay)
    {
        return false;
    }

    public function restore(User $user, Assay $assay)
    {
        return true;
    }

    public function forceDelete(User $user, Assay $assay)
    {
        return false;
    }
}
