<?php

namespace App\Policies;

use App\Models\SampleType;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SampleTypePolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, SampleType $sampleType)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, SampleType $sampleType)
    {
        return true;
    }

    public function delete(User $user, SampleType $sampleType)
    {
        return false;
    }

    public function restore(User $user, SampleType $sampleType)
    {
        return false;
    }

    public function forceDelete(User $user, SampleType $sampleType)
    {
        return false;
    }
}
