<?php

namespace App\Policies;

use App\Policies\Traits\OnlyAvailableForChosenStudy;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExperimentPolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;
}
