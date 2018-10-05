<?php

namespace App\Policies;

use App\User;
use Illuminate\Support\Facades\Auth;

class Authorization
{
    public const ADMINISTRATOR = 'Administrator';
    public const LABMANAGER = 'Lab Manager';
    public const MONITOR = 'Monitor';
    public const SCIENTIST = 'Scientist';

    public static function isAdministrator(User $user = null)
    {
        $user = $user ?? Auth::user();
        return $user->role == self::ADMINISTRATOR;
    }

    public static function isLabManager(User $user = null)
    {
        $user = $user ?? Auth::user();
        return in_array($user->role, [self::LABMANAGER, self::ADMINISTRATOR]);
    }

    public static function isScientist(User $user = null)
    {
        $user = $user ?? Auth::user();
        return in_array($user->role, [self::LABMANAGER, self::ADMINISTRATOR, self::SCIENTIST]);
    }

    public static function isMonitor(User $user = null)
    {
        $user = $user ?? Auth::user();
        return in_array($user->role, [self::LABMANAGER, self::ADMINISTRATOR, self::SCIENTIST, self::MONITOR]);
    }
}
