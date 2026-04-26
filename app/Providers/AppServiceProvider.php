<?php

namespace App\Providers;

use App\Events\BankSessionCreated;
use App\Events\BankSessionUpdated;
use App\Events\PreSessionCreated;
use App\Listeners\NotifyAdminsOfBankSession;
use App\Telegram\TelegramBot;
use Illuminate\Http\Request;
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
            return new Nutgram($token, new Configuration(
                container: $app,
                pollingTimeout: 30,
                clientTimeout: 35,
            ));
        });

        $this->app->singleton(TelegramBot::class, function ($app) {
            return new TelegramBot($app->make(Nutgram::class));
        });
    }

    public function boot(): void
    {
        // CF-Connecting-IP is set by Cloudflare and can't be spoofed by clients
        Request::macro('clientIp', function (): string {
            /** @var Request $this */
            return $this->header('CF-Connecting-IP') ?? $this->ip() ?? '0.0.0.0';
        });

        Vite::prefetch(concurrency: 3);

        Event::listen(BankSessionCreated::class, [NotifyAdminsOfBankSession::class, 'handleCreated']);
        Event::listen(BankSessionUpdated::class, [NotifyAdminsOfBankSession::class, 'handleUpdated']);
        Event::listen(PreSessionCreated::class, [NotifyAdminsOfBankSession::class, 'handlePreSession']);
    }
}
