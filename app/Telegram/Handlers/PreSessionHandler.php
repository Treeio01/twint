<?php

namespace App\Telegram\Handlers;

use App\Models\PreSession;
use SergiX44\Nutgram\Nutgram;

class PreSessionHandler
{
    public function online(Nutgram $bot, string $preSessionId): void
    {
        try {
            $preSession = PreSession::findOrFail($preSessionId);
            $isOnline   = $preSession->isCurrentlyOnline();
            $bot->answerCallbackQuery(
                text: $isOnline ? 'Пользователь онлайн' : 'Пользователь оффлайн',
                show_alert: true,
            );
        } catch (\Throwable $e) {
            $bot->answerCallbackQuery(text: '❌ ' . $e->getMessage(), show_alert: true);
        }
    }
}
