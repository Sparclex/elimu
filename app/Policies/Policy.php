<?php

namespace App\Policies;

use App\User;
use Illuminate\Support\Carbon;

abstract class Policy
{
    public function createdFiveMinutesAgo(User $user, $model)
    {
        return ($model->created_at->gt(Carbon::now()->subMinutes(5))
                && Authorization::isScientist($user)) || Authorization::isLabManager($user);
    }
}
