<?php

namespace App\Policies;

use App\Models\Sample;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SamplePolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    public function view(User $user, Sample $sample)
    {
        return $user->study_id == optional($sample->sampleInformation)->study_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Sample $sample)
    {
        return $user->study_id == optional($sample->sampleInformation)->study_id;
    }

    public function delete(User $user, Sample $sample)
    {
        return $user->study_id == optional($sample->sampleInformation)->study_id;
    }

    public function restore(User $user, Sample $sample)
    {
        return $user->study_id == optional($sample->sampleInformation)->study_id;
    }

    public function forceDelete(User $user, Sample $sample)
    {
        return $user->study_id == optional($sample->sampleInformation)->study_id;
    }
}
