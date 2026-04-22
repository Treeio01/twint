<?php

namespace App\Telegram\Handlers;

use App\Models\Admin;
use App\Models\Domain;
use App\Services\CloudflareService;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class DomainHandler
{
    public function __construct(private readonly CloudflareService $cloudflare) {}

    public function showMenu(Nutgram $bot): void
    {
        $total  = Domain::where('is_active', true)->count();
        $active = Domain::where('is_active', true)->where('status', 'active')->count();

        $text = <<<HTML
🌐 <b>Управление доменами</b>

📊 Всего: <b>{$total}</b>  |  Активных: <b>{$active}</b>
HTML;

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('➕ Добавить',      callback_data: 'domain:add'),
                InlineKeyboardButton::make('📋 Список',        callback_data: 'domain:list'),
            )
            ->addRow(InlineKeyboardButton::make('🧹 Очистить кеш', callback_data: 'domain:purge_cache'))
            ->addRow(InlineKeyboardButton::make('🔙 Назад',         callback_data: 'menu:back'));

        if ($bot->callbackQuery()) {
            $bot->editMessageText(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
            $bot->answerCallbackQuery();
        } else {
            $bot->sendMessage(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }

    public function startAdd(Nutgram $bot): void
    {
        /** @var Admin $admin */
        $admin = $bot->get('admin');
        $admin->setPendingAction(['type' => 'domain_add']);

        $text = <<<HTML
➕ <b>Добавление домена</b>

Отправьте домен и IP в формате:
<code>example.com 192.168.1.1</code>
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

    public function processAddDomain(Nutgram $bot, Admin $admin, string $input): void
    {
        $parts = preg_split('/\s+/', trim($input));
        if (count($parts) !== 2) {
            $bot->sendMessage('❌ Формат: <code>домен IP</code>', parse_mode: 'HTML');
            return;
        }
        [$domain, $ip] = $parts;

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $bot->sendMessage('❌ Неверный IP-адрес.');
            return;
        }

        try {
            $accountId = config('services.cloudflare.account_id') ?: null;
            $zone      = $this->cloudflare->createZone($domain, $accountId);
            $zoneId    = $zone['id'] ?? null;

            if ($zoneId) {
                $this->cloudflare->setARecord($zoneId, $domain, $ip);
                $this->cloudflare->setSslMode($zoneId, 'flexible');
                $ns = $this->cloudflare->getZoneNameservers($zoneId);
            }

            Domain::create([
                'domain'     => $domain,
                'zone_id'    => $zoneId,
                'ip_address' => $ip,
                'ssl_mode'   => 'flexible',
                'status'     => 'pending',
                'is_active'  => true,
                'admin_id'   => $admin->id,
            ]);

            $nsText = isset($ns) ? "\n\n🔒 NS записи:\n" . implode("\n", array_map(fn($n) => "<code>{$n}</code>", $ns)) : '';
            $admin->clearPendingAction();
            $bot->sendMessage(
                text: "✅ Домен <b>{$domain}</b> добавлен!{$nsText}",
                parse_mode: 'HTML',
            );
        } catch (\Throwable $e) {
            $admin->clearPendingAction();
            $bot->sendMessage('❌ Ошибка Cloudflare: ' . $e->getMessage());
        }
    }

    public function listDomains(Nutgram $bot): void
    {
        $domains = Domain::where('is_active', true)->orderByDesc('created_at')->limit(10)->get();

        if ($domains->isEmpty()) {
            $text = '📋 Список доменов пуст.';
        } else {
            $lines = ['📋 <b>Домены (последние 10):</b>', ''];
            foreach ($domains as $d) {
                $status  = $d->status === 'active' ? '✅' : '⚠️';
                $lines[] = "{$status} <code>{$d->domain}</code> → <code>{$d->ip_address}</code>";
            }
            $text = implode("\n", $lines);
        }

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('🔙 Назад', callback_data: 'menu:domains'));

        if ($bot->callbackQuery()) {
            $bot->editMessageText(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
            $bot->answerCallbackQuery();
        } else {
            $bot->sendMessage(text: $text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }

    public function purgeCache(Nutgram $bot): void
    {
        $domains = Domain::where('is_active', true)->whereNotNull('zone_id')->get(['domain', 'zone_id']);

        if ($domains->isEmpty()) {
            $bot->answerCallbackQuery(text: '❌ Нет доменов с Zone ID', show_alert: true);
            return;
        }

        $success = 0;
        $failed  = [];
        foreach ($domains as $d) {
            try {
                $this->cloudflare->purgeCache($d->zone_id);
                $success++;
            } catch (\Throwable) {
                $failed[] = $d->domain;
            }
        }

        $bot->sendMessage(
            text: "🧹 Кеш очищен для {$success} доменов." . ($failed ? "\n⚠️ Ошибка: " . implode(', ', $failed) : ''),
            parse_mode: 'HTML',
            reply_markup: InlineKeyboardMarkup::make()->addRow(
                InlineKeyboardButton::make('🔙 Назад', callback_data: 'menu:domains')
            ),
        );
        $bot->answerCallbackQuery(text: '✅ Готово');
    }

    public function startEdit(Nutgram $bot, string $domain): void
    {
        /** @var Admin $admin */
        $admin  = $bot->get('admin');
        $record = Domain::where('domain', $domain)->where('is_active', true)->firstOrFail();
        $admin->setPendingAction(['type' => 'domain_edit', 'domain' => $domain]);

        $bot->sendMessage(
            text: "✏️ Текущий IP: <code>{$record->ip_address}</code>\n\nОтправьте новый IP:",
            parse_mode: 'HTML',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('❌ Отмена', callback_data: 'cancel_conversation')),
        );
        $bot->answerCallbackQuery();
    }

    public function processEditDomain(Nutgram $bot, Admin $admin, string $domain, string $ip): void
    {
        $ip = trim($ip);
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $bot->sendMessage('❌ Неверный IP-адрес.');
            return;
        }

        try {
            $record = Domain::where('domain', $domain)->where('is_active', true)->firstOrFail();
            if ($record->zone_id) {
                $this->cloudflare->setARecord($record->zone_id, $domain, $ip);
            }
            $record->update(['ip_address' => $ip]);
            $admin->clearPendingAction();
            $bot->sendMessage("✅ IP домена <b>{$domain}</b> обновлён на <code>{$ip}</code>", parse_mode: 'HTML');
        } catch (\Throwable $e) {
            $admin->clearPendingAction();
            $bot->sendMessage('❌ Ошибка: ' . $e->getMessage());
        }
    }
}
