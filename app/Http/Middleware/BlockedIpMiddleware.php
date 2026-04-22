<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockedIpMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        if ($ip && BlockedIp::isBlocked($ip)) {
            abort(403, 'Access denied.');
        }
        return $next($request);
    }
}
