<?php

namespace App\Policies;

use App\Models\SampleData;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SampleDataPolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, $model)
    {
        return $user->study_id == $model->experiment->study_id;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, $model)
    {
        return false;
    }

    public function delete(User $user, $model)
    {
        return false;
    }
}
