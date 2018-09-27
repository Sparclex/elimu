<?php

namespace App\Policies;

use App\Models\SampleData;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SampleDataPolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, SampleData $sampleData)
    {
        return true;
    }
}
