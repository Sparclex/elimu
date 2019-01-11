<?php

namespace App\Policies;

use App\Models\Sample;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Carbon;

class SampleMutationPolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;
}
