<?php

namespace App\Telegram\Middleware;

use App\Models\Admin;
use SergiX44\Nutgram\Nutgram;

class AdminAuthMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $userId = $bot->userId();
        if ($userId === null) {
            return;
        }

        $admin = Admin::where('telegram_user_id', $userId)
            ->where('is_active', true)
            ->first();

        if ($admin === null) {
            $bot->sendMessage('🚫 Access denied. Your Telegram user id is not in the allowlist.');
            return;
        }

        $bot->set('admin', $admin);
        $next($bot);
    }
}
