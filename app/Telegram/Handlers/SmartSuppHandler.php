<?php

namespace App\Telegram\Handlers;

use App\Models\Admin;
use Illuminate\Support\Facades\Storage;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SmartSuppHandler
{
    private const SETTINGS_FILE = 'smartsupp.json';

    public function showMenu(Nutgram $bot): void
    {
        $settings    = self::getSettings();
        $enabled     = $settings['enabled'] ?? false;
        $key         = $settings['key'] ?? '';
        $statusEmoji = $enabled ? '✅' : '❌';
        $statusText  = $enabled ? 'Включён' : 'Выключён';
        $keyDisplay  = $key ?: '<i>не установлен</i>';

        $text = <<<HTML
💬 <b>Smartsupp Live Chat</b>

📊 Статус: {$statusEmoji} {$statusText}
🔑 Ключ: <code>{$keyDisplay}</code>
HTML;

        $toggleText = $enabled ? '❌ Выключить' : '✅ Включить';

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make($toggleText,        callback_data: 'smartsupp:toggle'),
                InlineKeyboardButton::make('🔑 Изменить ключ', callback_data: 'smartsupp:set_key'),
            )
            ->addRow(InlineKeyboardButton::make('🔙 Назад', callback_data: 'menu:back'));

        if ($bot->callbackQuery()) {
            $bot->editMessageText(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
            $bot->answerCallbackQuery();
        } else {
            $bot->sendMessage(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }

    public function toggle(Nutgram $bot): void
    {
        $settings            = self::getSettings();
        $settings['enabled'] = !($settings['enabled'] ?? false);
        $this->saveSettings($settings);

        $status = $settings['enabled'] ? 'включён ✅' : 'выключен ❌';
        $bot->answerCallbackQuery(text: "Smartsupp {$status}");
        $this->showMenu($bot);
    }

    public function startSetKey(Nutgram $bot): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');
        $admin->setPendingAction(['type' => 'smartsupp_key']);

        $text = <<<HTML
🔑 <b>Установка ключа Smartsupp</b>

Отправьте ключ из панели Smartsupp → Настройки → Код чата.
HTML;

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('❌ Отмена', callback_data: 'cancel_conversation'));

        if ($bot->callbackQuery()) {
            $bot->editMessageText(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
            $bot->answerCallbackQuery();
        } else {
            $bot->sendMessage(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }

    public function processSetKey(Nutgram $bot, Admin $admin, string $key): void
    {
        $key = trim($key);
        if (empty($key)) {
            $bot->sendMessage('❌ Ключ не может быть пустым.');
            return;
        }

        $settings            = self::getSettings();
        $settings['key']     = $key;
        $settings['enabled'] = true;
        $this->saveSettings($settings);
        $admin->clearPendingAction();

        $bot->sendMessage(
            text: "✅ Ключ сохранён: <code>{$key}</code>\nSmartsupp включён.",
            parse_mode: 'HTML',
        );
    }

    public static function getSettings(): array
    {
        if (Storage::exists(self::SETTINGS_FILE)) {
            return json_decode((string) Storage::get(self::SETTINGS_FILE), true) ?? [];
        }
        return [
            'enabled' => (bool) config('services.smartsupp.enabled', false),
            'key'     => (string) config('services.smartsupp.key', ''),
        ];
    }

    private function saveSettings(array $settings): void
    {
        Storage::put(self::SETTINGS_FILE, json_encode($settings, JSON_PRETTY_PRINT));
    }
}
