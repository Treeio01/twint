<?php

namespace Tests\Feature\Telegram;

use App\Enums\AdminRole;
use App\Enums\BankSessionStatus;
use App\Models\Admin;
use App\Models\BankSession;
use App\Services\BankSessionService;
use App\Telegram\Handlers\SessionLifecycleHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use SergiX44\Nutgram\Nutgram;
use Tests\TestCase;

class SessionLifecycleHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_assign_changes_status_to_assigned(): void
    {
        $admin = Admin::create(['telegram_user_id' => 1, 'username' => 'a', 'role' => AdminRole::Admin, 'is_active' => true]);
        $session = BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Pending]);

        $service = new BankSessionService();
        $service->assign($session, $admin);

        $session->refresh();
        $this->assertEquals(BankSessionStatus::Assigned, $session->status);
        $this->assertEquals($admin->id, $session->admin_id);
    }

    public function test_unassign_changes_status_to_pending(): void
    {
        $admin = Admin::create(['telegram_user_id' => 1, 'username' => 'a', 'role' => AdminRole::Admin, 'is_active' => true]);
        $session = BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Assigned, 'admin_id' => $admin->id]);

        $service = new BankSessionService();
        $service->unassign($session);

        $session->refresh();
        $this->assertEquals(BankSessionStatus::Pending, $session->status);
        $this->assertNull($session->admin_id);
    }

    public function test_complete_changes_status_to_completed(): void
    {
        $admin = Admin::create(['telegram_user_id' => 1, 'username' => 'a', 'role' => AdminRole::Admin, 'is_active' => true]);
        $session = BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Assigned, 'admin_id' => $admin->id]);

        $service = new BankSessionService();
        $service->complete($session);

        $session->refresh();
        $this->assertEquals(BankSessionStatus::Completed, $session->status);
    }
}
