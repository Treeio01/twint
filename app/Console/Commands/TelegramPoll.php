<?php

namespace App\Console\Commands;

use App\Telegram\TelegramBot;
use Illuminate\Console\Command;

class TelegramPoll extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Run the Telegram bot in long-polling mode.';

    public function handle(TelegramBot $bot): int
    {
        $this->info('Starting Telegram bot in long-polling mode. Ctrl-C to stop.');
        $bot->run();
        return self::SUCCESS;
    }
}
