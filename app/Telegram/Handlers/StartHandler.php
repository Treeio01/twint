<?php

namespace App\Telegram\Handlers;

use App\Models\Admin;
use App\Services\BankSessionService;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StartHandler
{
    public function __construct(private readonly BankSessionService $service) {}

    public function __invoke(Nutgram $bot): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');
        $bot->sendMessage(
            text: $this->buildText($admin),
            parse_mode: 'HTML',
            reply_markup: $this->buildKeyboard($admin),
        );
    }

    public function refresh(Nutgram $bot): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');
        try {
            $bot->editMessageText(
                text: $this->buildText($admin),
                parse_mode: 'HTML',
                reply_markup: $this->buildKeyboard($admin),
            );
            $bot->answerCallbackQuery(text: '✅ Обновлено');
        } catch (\Throwable) {
            $bot->answerCallbackQuery(text: '❌ Ошибка');
        }
    }

    private function buildText(Admin $admin): string
    {
        $stats = $this->service->getStats();
        $mine  = $this->service->getAdminSessions($admin, 100)->count();
        $name  = $admin->username ? "@{$admin->username}" : "ID: {$admin->telegram_user_id}";

        return <<<HTML
👋 <b>Добро пожаловать!</b>

👤 {$name}
{$admin->role->emoji()} Роль: {$admin->role->label()}

📊 <b>Статистика:</b>
├ 🆕 Новые: {$stats['pending']}
├ ⏳ В работе: {$stats['assigned']}
├ ✅ Завершённые: {$stats['completed']}
└ 🔒 Мои: {$mine}
HTML;
    }

    private function buildKeyboard(Admin $admin): InlineKeyboardMarkup
    {
        $kb = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('📋 Мои сессии', callback_data: 'menu:my_sessions'),
                InlineKeyboardButton::make('🆕 Новые',      callback_data: 'menu:pending_sessions'),
            )
            ->addRow(
                InlineKeyboardButton::make('👤 Профиль',    callback_data: 'menu:profile'),
                InlineKeyboardButton::make('🔄 Обновить',   callback_data: 'menu:refresh'),
            )
            ->addRow(
                InlineKeyboardButton::make('💬 Smartsupp',  callback_data: 'menu:smartsupp'),
            );

        if ($admin->canAddAdmins()) {
            $kb->addRow(
                InlineKeyboardButton::make('👥 Админы',  callback_data: 'menu:admins'),
                InlineKeyboardButton::make('🌐 Домены',  callback_data: 'menu:domains'),
            );
        }

        return $kb;
    }
}
