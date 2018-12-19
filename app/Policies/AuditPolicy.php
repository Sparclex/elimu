<?php

namespace App\Policies;

use App\User;

class AuditPolicy
{
    public function view(User $user, $model)
    {
        return true;
    }

    public function create(User $user)
    {
        return false;
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
