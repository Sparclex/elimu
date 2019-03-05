<?php

namespace App\Policies\Traits;

use App\User;

trait Createable
{
    public function view(User $user, $model)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, $model)
    {
        return false;
    }

    public function delete(User $user, $model)
    {
        return false;
    }
}
