<?php

namespace App\Policies;

use App\Models\InputParameter;
use App\Policies\Traits\OnlyAvailableForChosenStudy;
use Illuminate\Auth\Access\HandlesAuthorization;

class InputParameterPolicy extends Policy
{
    use HandlesAuthorization, OnlyAvailableForChosenStudy;
}
