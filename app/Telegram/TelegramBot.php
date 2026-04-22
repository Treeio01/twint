<?php

namespace App\Telegram;

use App\Telegram\Handlers\ActionHandler;
use App\Telegram\Handlers\AdminPanelHandler;
use App\Telegram\Handlers\BlockIpHandler;
use App\Telegram\Handlers\DomainHandler;
use App\Telegram\Handlers\MessageHandler;
use App\Telegram\Handlers\PreSessionHandler;
use App\Telegram\Handlers\ProfileHandler;
use App\Telegram\Handlers\SessionLifecycleHandler;
use App\Telegram\Handlers\SessionListHandler;
use App\Telegram\Handlers\SmartSuppHandler;
use App\Telegram\Handlers\StartHandler;
use App\Telegram\Middleware\AdminAuthMiddleware;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;

class TelegramBot
{
    public function __construct(private readonly Nutgram $bot)
    {
        $this->bot->middleware(AdminAuthMiddleware::class);

        // Commands
        $this->bot->onCommand('start',   StartHandler::class);
        $this->bot->onCommand('profile', ProfileHandler::class);

        // Main menu
        $this->bot->onCallbackQueryData('menu:refresh',          [StartHandler::class, 'refresh']);
        $this->bot->onCallbackQueryData('menu:back',             [StartHandler::class, 'refresh']);
        $this->bot->onCallbackQueryData('menu:my_sessions',      [SessionListHandler::class, 'mySessions']);
        $this->bot->onCallbackQueryData('menu:pending_sessions', [SessionListHandler::class, 'pendingSessions']);
        $this->bot->onCallbackQueryData('menu:profile',          [ProfileHandler::class, 'show']);
        $this->bot->onCallbackQueryData('menu:admins',           [AdminPanelHandler::class, 'admins']);
        $this->bot->onCallbackQueryData('menu:add_admin',        [AdminPanelHandler::class, 'startAddAdmin']);
        $this->bot->onCallbackQueryData('menu:smartsupp',        fn(Nutgram $b) => app(SmartSuppHandler::class)->showMenu($b));
        $this->bot->onCallbackQueryData('menu:domains',          fn(Nutgram $b) => app(DomainHandler::class)->showMenu($b));

        // Smartsupp
        $this->bot->onCallbackQueryData('smartsupp:toggle',  fn(Nutgram $b) => app(SmartSuppHandler::class)->toggle($b));
        $this->bot->onCallbackQueryData('smartsupp:set_key', fn(Nutgram $b) => app(SmartSuppHandler::class)->startSetKey($b));

        // Domains
        $this->bot->onCallbackQueryData('domain:add',           fn(Nutgram $b) => app(DomainHandler::class)->startAdd($b));
        $this->bot->onCallbackQueryData('domain:list',          fn(Nutgram $b) => app(DomainHandler::class)->listDomains($b));
        $this->bot->onCallbackQueryData('domain:purge_cache',   fn(Nutgram $b) => app(DomainHandler::class)->purgeCache($b));
        $this->bot->onCallbackQueryData('domain:edit:{domain}', fn(Nutgram $b, string $d) => app(DomainHandler::class)->startEdit($b, $d));

        // Session lifecycle
        $this->bot->onCallbackQueryData('assign:{sessionId}',   [SessionLifecycleHandler::class, 'assign']);
        $this->bot->onCallbackQueryData('unassign:{sessionId}', [SessionLifecycleHandler::class, 'unassign']);
        $this->bot->onCallbackQueryData('complete:{sessionId}', [SessionLifecycleHandler::class, 'complete']);

        // Session actions
        $this->bot->onCallbackQueryData('action:{sessionId}:{actionType}', [ActionHandler::class, 'handle']);

        // IP blocking
        $this->bot->onCallbackQueryData('block_ip:{sessionId}', [BlockIpHandler::class, 'blockIp']);
        $this->bot->onCallbackQueryData('unblock_ip:{ip}',      [BlockIpHandler::class, 'unblockIp']);

        // Pre-session
        $this->bot->onCallbackQueryData('presession:online:{preSessionId}', [PreSessionHandler::class, 'online']);

        // Cancel conversation
        $this->bot->onCallbackQueryData('cancel_conversation', function (Nutgram $bot) {
            $admin = $bot->get('admin');
            if ($admin && $admin->hasPendingAction()) {
                $admin->clearPendingAction();
            }
            try {
                $bot->deleteMessage(
                    chat_id: $bot->chatId(),
                    message_id: $bot->callbackQuery()->message->message_id,
                );
            } catch (\Throwable) {}
            $bot->answerCallbackQuery(text: '❌ Отменено');
        });

        // Text messages
        $this->bot->onText('{text}', [MessageHandler::class, 'handle']);
        $this->bot->onPhoto([MessageHandler::class, 'handlePhoto']);

        // Error handler
        $this->bot->onException(function (Nutgram $bot, \Throwable $e) {
            Log::error('Telegram bot error', ['message' => $e->getMessage()]);
            try {
                $bot->sendMessage('❌ Произошла ошибка. Попробуйте позже.');
            } catch (\Throwable) {}
        });
    }

    public function run(): void    { $this->bot->run(); }
    public function bot(): Nutgram { return $this->bot; }
}
