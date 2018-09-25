<?php

namespace App\Policies;

use App\Models\InputParameter;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InputParameterPolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, InputParameter $inputParameter)
    {
        return $user->study_id == $inputParameter->study_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, InputParameter $inputParameter)
    {
        return $user->study_id == $inputParameter->study_id;
    }

    public function delete(User $user, InputParameter $inputParameter)
    {
        return $user->study_id == $inputParameter->study_id;
    }

    public function restore(User $user, InputParameter $inputParameter)
    {
        return $user->study_id == $inputParameter->study_id;
    }

    public function forceDelete(User $user, InputParameter $inputParameter)
    {
        return $user->study_id == $inputParameter->study_id;
    }
}
