<?php

namespace App\Policies;

use App\User;

trait OnlyAvailableForChosenStudy
{
    public function viewAny(User $user) {
        return $user->study_id;
    }
}
