<?php

namespace Sparclex\Lims\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sparclex\Lims\Tool;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
