<?php

namespace App\Telegram\Handlers;

use App\Models\Admin;
use SergiX44\Nutgram\Nutgram;

class StartHandler
{
    public function __invoke(Nutgram $bot): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');
        $bot->sendMessage(
            text: sprintf(
                "👋 Hi, %s. Role: <b>%s</b>. You will receive cards when new sessions appear.",
                $admin->username ?? (string) $admin->telegram_user_id,
                $admin->role->value,
            ),
            parse_mode: 'HTML',
        );
    }
}
