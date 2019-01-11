<?php

namespace App\Policies;

use App\Models\Sample;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class SamplePolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, Sample $sample)
    {
        return $user->study_id == $sample->study_id;
    }

    public function create(User $user)
    {
        return $user->isScientist();
    }

    public function update(User $user, Sample $sample)
    {
        return $sample->study_id == $user->study_id &&  $user->isScientist();
    }

    public function delete(User $user, Sample $sample)
    {
        return $sample->study_id == $user->study_id &&  $user->isScientist();
    }
}
