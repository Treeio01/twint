<?php

namespace App\Telegram;

use App\Telegram\Handlers\ActionHandler;
use App\Telegram\Handlers\MessageHandler;
use App\Telegram\Handlers\StartHandler;
use App\Telegram\Middleware\AdminAuthMiddleware;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;

class TelegramBot
{
    public function __construct(private readonly Nutgram $bot)
    {
        $this->bot->middleware(AdminAuthMiddleware::class);

        $this->bot->onCommand('start', StartHandler::class);

        $this->bot->onCallbackQueryData(
            'action:{sessionId}:{actionType}',
            [ActionHandler::class, 'handle'],
        );

        $this->bot->onText('{text}', [MessageHandler::class, 'handle']);

        $this->bot->onException(function (Nutgram $bot, \Throwable $e) {
            Log::error('Telegram bot error', ['message' => $e->getMessage()]);
            try {
                $bot->sendMessage('❌ Internal error, please retry.');
            } catch (\Throwable) {
            }
        });
    }

    public function run(): void
    {
        $this->bot->run();
    }

    public function bot(): Nutgram
    {
        return $this->bot;
    }
}
