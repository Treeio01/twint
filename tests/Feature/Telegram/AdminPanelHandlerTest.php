<?php

namespace Tests\Feature\Telegram;

use App\Enums\AdminRole;
use App\Models\Admin;
use App\Telegram\Handlers\AdminPanelHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use SergiX44\Nutgram\Nutgram;
use Tests\TestCase;

class AdminPanelHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_add_admin_creates_admin(): void
    {
        $superadmin = Admin::create([
            'telegram_user_id' => 1,
            'username'         => 'super',
            'role'             => AdminRole::Superadmin,
            'is_active'        => true,
        ]);

        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('sendMessage')->once();

        $handler = new AdminPanelHandler();
        $handler->processAddAdmin($bot, $superadmin, '999999');

        $this->assertDatabaseHas('admins', ['telegram_user_id' => 999999]);
    }

    public function test_process_add_admin_rejects_duplicate(): void
    {
        $superadmin = Admin::create(['telegram_user_id' => 1, 'role' => 'superadmin', 'is_active' => true]);
        Admin::create(['telegram_user_id' => 2, 'role' => 'admin', 'is_active' => true]);

        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('sendMessage')->once();

        (new AdminPanelHandler())->processAddAdmin($bot, $superadmin, '2');

        $this->assertDatabaseCount('admins', 2);
    }
}
