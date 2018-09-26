<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SampleDataPolicy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}
