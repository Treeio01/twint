<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.bank_auth_admin_token') ?: env('BANK_AUTH_ADMIN_TOKEN');
        if (!$expected || $request->header('X-Admin-Token') !== $expected) {
            abort(403, 'Invalid admin token');
        }
        return $next($request);
    }
}
