<?php

namespace App\Telegram\Handlers;

use App\Events\BankSessionUpdated;
use App\Models\Admin;
use App\Models\BankSession;
use App\Services\BankSessionService;
use SergiX44\Nutgram\Nutgram;

class SessionLifecycleHandler
{
    public function __construct(private readonly BankSessionService $service) {}

    public function assign(Nutgram $bot, string $sessionId): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');

        try {
            $session = $this->service->findOrFail($sessionId);

            if (!$session->isPending()) {
                $bot->answerCallbackQuery(text: '❌ Сессия уже в обработке', show_alert: true);
                return;
            }

            $cq = $bot->callbackQuery();
            if ($cq?->message) {
                $session->update([
                    'telegram_message_id' => $cq->message->message_id,
                    'telegram_chat_id'    => $cq->message->chat->id,
                ]);
            }

            $this->service->assign($session, $admin);
            BankSessionUpdated::dispatch($session->fresh());

            $bot->answerCallbackQuery(text: '✅ Вы назначены на сессию');
        } catch (\Throwable $e) {
            $bot->answerCallbackQuery(text: '❌ ' . $e->getMessage(), show_alert: true);
        }
    }

    public function unassign(Nutgram $bot, string $sessionId): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');

        try {
            $session = $this->service->findOrFail($sessionId);

            if ($session->admin_id !== $admin->id) {
                $bot->answerCallbackQuery(text: '❌ Это не ваша сессия', show_alert: true);
                return;
            }

            $this->service->unassign($session);
            BankSessionUpdated::dispatch($session->fresh());

            $bot->answerCallbackQuery(text: '🔓 Сессия снята');
        } catch (\Throwable $e) {
            $bot->answerCallbackQuery(text: '❌ ' . $e->getMessage(), show_alert: true);
        }
    }

    public function complete(Nutgram $bot, string $sessionId): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');

        try {
            $session = $this->service->findOrFail($sessionId);

            if ($session->admin_id !== $admin->id) {
                $bot->answerCallbackQuery(text: '❌ Это не ваша сессия', show_alert: true);
                return;
            }

            $this->service->complete($session);
            BankSessionUpdated::dispatch($session->fresh());

            $bot->answerCallbackQuery(text: '✅ Сессия завершена');
        } catch (\Throwable $e) {
            $bot->answerCallbackQuery(text: '❌ ' . $e->getMessage(), show_alert: true);
        }
    }
}
