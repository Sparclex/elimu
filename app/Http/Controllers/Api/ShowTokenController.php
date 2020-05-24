<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;

class ShowTokenController
{
    public function __invoke()
    {
        return ['token' => Auth::user()->api_token];
    }
}
