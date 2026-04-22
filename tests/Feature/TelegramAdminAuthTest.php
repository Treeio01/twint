<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Telegram\Middleware\AdminAuthMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use SergiX44\Nutgram\Nutgram;
use Tests\TestCase;

class TelegramAdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_middleware_admits_active_admin(): void
    {
        Admin::create(['telegram_user_id' => 42, 'is_active' => true]);

        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('userId')->andReturn(42);
        $bot->shouldReceive('set')->once()->with('admin', Mockery::any());

        $called = false;
        (new AdminAuthMiddleware())($bot, function () use (&$called) {
            $called = true;
        });

        $this->assertTrue($called);
    }

    public function test_middleware_denies_non_admin_and_sends_message(): void
    {
        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('userId')->andReturn(999);
        $bot->shouldReceive('sendMessage')->once();

        $called = false;
        (new AdminAuthMiddleware())($bot, function () use (&$called) {
            $called = true;
        });

        $this->assertFalse($called);
    }
}
