<?php

namespace App\Policies;

use App\Models\Storage;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoragePolicy extends Policy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->study_id;
    }

    public function view(User $user, Storage $storage)
    {
        return $user->study_id == $storage->study_id;
    }
}
