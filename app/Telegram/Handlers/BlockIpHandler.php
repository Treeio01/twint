<?php

namespace App\Telegram\Handlers;

use App\Models\Admin;
use App\Models\BankSession;
use App\Models\BlockedIp;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class BlockIpHandler
{
    public function blockIp(Nutgram $bot, string $sessionId): void
    {
        /** @var Admin $admin */
        $admin   = $bot->get('admin');
        $session = BankSession::findOrFail($sessionId);

        if (!$session->ip_address) {
            $bot->answerCallbackQuery(text: '❌ У сессии нет IP', show_alert: true);
            return;
        }

        if (BlockedIp::isBlocked($session->ip_address)) {
            $bot->answerCallbackQuery(text: '⚠️ IP уже заблокирован', show_alert: true);
            return;
        }

        $admin->setPendingAction(['type' => 'block_ip', 'sessionId' => $sessionId]);

        $bot->sendMessage(
            text: "🚫 Заблокировать IP <code>{$session->ip_address}</code>?\n\nОтправьте <b>*</b> для подтверждения.",
            parse_mode: 'HTML',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('❌ Отмена', callback_data: 'cancel_conversation')),
        );
        $bot->answerCallbackQuery();
    }

    public function confirmBlock(Nutgram $bot, Admin $admin, string $sessionId): void
    {
        $session = BankSession::find($sessionId);
        if (!$session?->ip_address) {
            $bot->sendMessage('❌ Сессия или IP не найдены.');
            return;
        }

        BlockedIp::create([
            'ip_address' => $session->ip_address,
            'reason'     => 'Blocked by operator via Telegram',
            'admin_id'   => $admin->id,
        ]);

        $bot->sendMessage("✅ IP <code>{$session->ip_address}</code> заблокирован.", parse_mode: 'HTML');
    }

    public function unblockIp(Nutgram $bot, string $ip): void
    {
        $record = BlockedIp::where('ip_address', $ip)->first();
        if (!$record) {
            $bot->answerCallbackQuery(text: '❌ IP не найден в списке блокировок', show_alert: true);
            return;
        }

        $record->delete();
        $bot->answerCallbackQuery(text: "✅ IP {$ip} разблокирован");
    }
}
