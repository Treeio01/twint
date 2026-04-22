<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale', 'de');
        app()->setLocale($locale);
        Inertia::share('locale', $locale);

        return $next($request);
    }
}
