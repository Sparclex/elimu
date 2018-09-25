<?php

namespace App\Policies;

use App\Models\SampleInformation;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SampleInformationPolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, SampleInformation $sampleInformation)
    {
        return $user->study_id == $sampleInformation->study_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, SampleInformation $sampleInformation)
    {
        return $user->study_id == $sampleInformation->study_id;
    }

    public function delete(User $user, SampleInformation $sampleInformation)
    {
        return $user->study_id == $sampleInformation->study_id;
    }

    public function restore(User $user, SampleInformation $sampleInformation)
    {
        return $user->study_id == $sampleInformation->study_id;
    }

    public function forceDelete(User $user, SampleInformation $sampleInformation)
    {
        return $user->study_id == $sampleInformation->study_id;
    }
}
