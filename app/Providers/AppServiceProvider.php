<?php

namespace App\Providers;

use App\Events\BankSessionCreated;
use App\Events\BankSessionUpdated;
use App\Listeners\NotifyAdminsOfBankSession;
use App\Telegram\TelegramBot;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Nutgram;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Nutgram::class, function ($app) {
            $token = (string) config('services.telegram.bot_token');
            if ($token === '') {
                throw new \RuntimeException('TELEGRAM_BOT_TOKEN is not configured.');
            }
            return new Nutgram($token, new Configuration(container: $app));
        });

        $this->app->singleton(TelegramBot::class, function ($app) {
            return new TelegramBot($app->make(Nutgram::class));
        });
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Event::listen(BankSessionCreated::class, [NotifyAdminsOfBankSession::class, 'handleCreated']);
        Event::listen(BankSessionUpdated::class, [NotifyAdminsOfBankSession::class, 'handleUpdated']);
    }
}
