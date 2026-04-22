<?php

namespace App\Listeners;

use App\Events\BankSessionCreated;
use App\Events\BankSessionUpdated;
use App\Models\Admin;
use App\Services\Telegram\TelegramCardBuilder;
use SergiX44\Nutgram\Nutgram;

class NotifyAdminsOfBankSession
{
    public function __construct(
        private readonly Nutgram $bot,
        private readonly TelegramCardBuilder $builder,
    ) {
    }

    public function handleCreated(BankSessionCreated $event): void
    {
        $session = $event->session;
        $text = $this->builder->buildCardText($session);
        $keyboard = $this->builder->buildKeyboard($session);

        $admins = Admin::where('is_active', true)->get();
        foreach ($admins as $admin) {
            try {
                $msg = $this->bot->sendMessage(
                    text: $text,
                    chat_id: $admin->telegram_user_id,
                    parse_mode: 'HTML',
                    reply_markup: $keyboard,
                );
                if ($session->telegram_message_id === null && $msg !== null) {
                    $session->telegram_message_id = $msg->message_id;
                    $session->telegram_chat_id = $admin->telegram_user_id;
                    $session->save();
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed to deliver card to admin', [
                    'admin_id' => $admin->id,
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function handleUpdated(BankSessionUpdated $event): void
    {
        $session = $event->session;
        if ($session->telegram_chat_id === null || $session->telegram_message_id === null) {
            return;
        }
        $text = $this->builder->buildCardText($session);
        $keyboard = $this->builder->buildKeyboard($session);
        try {
            $this->bot->editMessageText(
                text: $text,
                chat_id: $session->telegram_chat_id,
                message_id: $session->telegram_message_id,
                parse_mode: 'HTML',
                reply_markup: $keyboard,
            );
        } catch (\Throwable $e) {
            logger()->warning('Failed to edit card', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
