<?php

namespace App\Telegram\Handlers;

use App\Models\Admin;
use App\Services\BankSessionService;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class ProfileHandler
{
    public function __construct(private readonly BankSessionService $service) {}

    public function __invoke(Nutgram $bot): void
    {
        $this->show($bot);
    }

    public function show(Nutgram $bot): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');
        $name  = $admin->username ? "@{$admin->username}" : "ID: {$admin->telegram_user_id}";
        $mine  = $this->service->getAdminSessions($admin, 100)->count();

        $text = <<<HTML
👤 <b>Профиль администратора</b>

{$admin->role->emoji()} {$name}
🏷 Роль: {$admin->role->label()}
📋 Сессий обработано: {$mine}
HTML;

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('📋 Мои сессии', callback_data: 'menu:my_sessions'),
                InlineKeyboardButton::make('🔙 Назад',      callback_data: 'menu:back'),
            );

        if ($bot->callbackQuery()) {
            $bot->editMessageText(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
            $bot->answerCallbackQuery();
        } else {
            $bot->sendMessage(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }
}
