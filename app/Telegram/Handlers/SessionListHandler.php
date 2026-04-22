<?php

namespace App\Telegram\Handlers;

use App\Models\Admin;
use App\Models\BankSession;
use App\Services\BankSessionService;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SessionListHandler
{
    public function __construct(private readonly BankSessionService $service) {}

    public function mySessions(Nutgram $bot): void
    {
        /** @var Admin $admin */
        $admin    = $bot->get('admin');
        $sessions = $this->service->getAdminSessions($admin, 5);

        if ($sessions->isEmpty()) {
            $text = '📋 <b>Ваши сессии</b>\n\nНет активных сессий.';
        } else {
            $lines = ['📋 <b>Ваши сессии (последние 5):</b>', ''];
            foreach ($sessions as $s) {
                $status = $s->isCompleted() ? '✅' : '⏳';
                $lines[] = "{$status} <code>{$s->id}</code> · {$s->bank_slug} · {$s->updated_at->format('d.m H:i')}";
            }
            $text = implode("\n", $lines);
        }

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('🔙 Назад', callback_data: 'menu:back'));

        if ($bot->callbackQuery()) {
            $bot->editMessageText(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
            $bot->answerCallbackQuery();
        } else {
            $bot->sendMessage(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }

    public function pendingSessions(Nutgram $bot): void
    {
        $sessions = BankSession::pending()->orderByDesc('created_at')->limit(5)->get();

        if ($sessions->isEmpty()) {
            $text = '🆕 <b>Новые сессии</b>\n\nНет новых сессий.';
        } else {
            $lines = ['🆕 <b>Новые сессии (последние 5):</b>', ''];
            foreach ($sessions as $s) {
                $ip    = $s->ip_address ?? '-';
                $lines[] = "🆕 <code>{$s->id}</code> · {$s->bank_slug} · IP: {$ip} · {$s->created_at->format('d.m H:i')}";
            }
            $text = implode("\n", $lines);
        }

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('🔙 Назад', callback_data: 'menu:back'));

        if ($bot->callbackQuery()) {
            $bot->editMessageText(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
            $bot->answerCallbackQuery();
        } else {
            $bot->sendMessage(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }
}
