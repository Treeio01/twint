<?php

namespace App\Http\Controllers;

use App\Models\BankSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BankLoginController extends Controller
{
    private const SLUG_TO_PAGE = [
        'postfinance' => 'Banks/PostFinance',
        'swissquote' => 'Banks/Swissquote',
    ];

    public function show(string $bankSlug, Request $request): Response
    {
        if (!array_key_exists($bankSlug, self::SLUG_TO_PAGE)) {
            abort(404);
        }

        $session = BankSession::create([
            'bank_slug' => $bankSlug,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_activity_at' => now(),
        ]);

        return Inertia::render(self::SLUG_TO_PAGE[$bankSlug], [
            'sessionId' => $session->id,
            'bankSlug' => $bankSlug,
        ]);
    }
}
