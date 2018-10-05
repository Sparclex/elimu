<?php

namespace App\Policies;

use App\Models\SampleInformation;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Carbon;

class SampleInformationPolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;
}
