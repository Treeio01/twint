<?php

namespace App\Telegram\Handlers;

use App\Enums\AdminRole;
use App\Models\Admin;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class AdminPanelHandler
{
    public function admins(Nutgram $bot): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');
        if (!$admin->canAddAdmins()) {
            $bot->answerCallbackQuery(text: '❌ Нет доступа', show_alert: true);
            return;
        }

        $admins = Admin::where('is_active', true)->orderBy('id')->get();
        $lines  = ['👥 <b>Администраторы:</b>', ''];
        foreach ($admins as $a) {
            $name   = $a->username ? "@{$a->username}" : "ID:{$a->telegram_user_id}";
            $lines[] = "{$a->role->emoji()} {$name} · {$a->role->label()}";
        }

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('➕ Добавить', callback_data: 'menu:add_admin'))
            ->addRow(InlineKeyboardButton::make('🔙 Назад',    callback_data: 'menu:back'));

        if ($bot->callbackQuery()) {
            $bot->editMessageText(text: implode("\n", $lines), parse_mode: 'HTML', reply_markup: $keyboard);
            $bot->answerCallbackQuery();
        } else {
            $bot->sendMessage(text: implode("\n", $lines), parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }

    public function startAddAdmin(Nutgram $bot): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');
        if (!$admin->canAddAdmins()) {
            $bot->answerCallbackQuery(text: '❌ Нет доступа', show_alert: true);
            return;
        }

        $admin->setPendingAction(['type' => 'admin_add']);

        $text = <<<HTML
➕ <b>Добавление администратора</b>

Отправьте Telegram ID нового администратора (числовой):

<b>Пример:</b> <code>123456789</code>
HTML;

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('❌ Отмена', callback_data: 'cancel_conversation'));

        $bot->sendMessage(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
        $bot->answerCallbackQuery();
    }

    public function processAddAdmin(Nutgram $bot, Admin $admin, string $input): void
    {
        $input = trim($input);
        if (!is_numeric($input)) {
            $bot->sendMessage('❌ Telegram ID должен быть числом.');
            return;
        }

        $telegramId = (int) $input;
        $existing   = Admin::where('telegram_user_id', $telegramId)->first();

        if ($existing) {
            $admin->clearPendingAction();
            $bot->sendMessage("⚠️ Администратор с ID {$telegramId} уже существует.");
            return;
        }

        $newAdmin = Admin::create([
            'telegram_user_id' => $telegramId,
            'role'             => AdminRole::Admin,
            'is_active'        => true,
        ]);

        $admin->clearPendingAction();
        $bot->sendMessage(
            text: "✅ Администратор <code>{$telegramId}</code> добавлен с ролью: {$newAdmin->role->label()}",
            parse_mode: 'HTML',
        );
    }
}
