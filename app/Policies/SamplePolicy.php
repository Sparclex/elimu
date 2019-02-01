<?php

namespace App\Policies;

use App\Models\Sample;
use App\Models\SampleType;
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

    /**
     * Determine whether the user can attach a tag to a podcast.
     *
     * @param  \App\User  $user
     * @param  \App\Podcast  $podcast
     * @param  \App\Tag  $tag
     * @return mixed
     */
    public function attachSampleType()
    {
        return false;
    }

    /**
     * Determine whether the user can detach a tag from a podcast.
     *
     * @param  \App\User  $user
     * @param  \App\Podcast  $podcast
     * @param  \App\Tag  $tag
     * @return mixed
     */
    public function detachSampleType()
    {
        return false;
    }
}
