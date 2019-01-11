<?php

namespace App\Policies;

use App\Models\Experiment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExperimentPolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;
}
