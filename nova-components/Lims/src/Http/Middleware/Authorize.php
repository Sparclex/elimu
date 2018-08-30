<?php

namespace Sparclex\Lims\Http\Middleware;

use Sparclex\Lims\Lims;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return resolve(Lims::class)->authorize($request) ? $next($request) : abort(403);
    }
}
