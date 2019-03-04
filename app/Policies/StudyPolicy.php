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
        return $user->is_admin || $user->managedStudies()->exists();
    }

    public function view(User $user, Study $study)
    {
        return $user->is_admin || $user->managedStudies()
                ->wherePivot('study_id', $study->id)
                ->exists();
    }

    public function create(User $user)
    {
        return $user->is_admin;
    }

    public function update(User $user, Study $study)
    {
        return $user->is_admin || $user->managedStudies()
                ->wherePivot('study_id', $study->id)
                ->exists();
    }

    public function delete(User $user, Study $study)
    {
        return $user->is_admin;
    }
}
