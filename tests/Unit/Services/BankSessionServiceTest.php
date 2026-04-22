<?php

namespace Tests\Unit\Services;

use App\Enums\BankSessionStatus;
use App\Models\Admin;
use App\Models\BankSession;
use App\Services\BankSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankSessionServiceTest extends TestCase
{
    use RefreshDatabase;

    private BankSessionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BankSessionService();
    }

    public function test_get_stats_counts_by_status(): void
    {
        BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Pending]);
        BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Assigned]);
        BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Completed]);

        $stats = $this->service->getStats();

        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(1, $stats['assigned']);
        $this->assertEquals(1, $stats['completed']);
    }

    public function test_get_admin_sessions_filters_by_admin_id(): void
    {
        $admin = Admin::create([
            'telegram_user_id' => 111,
            'username' => 'test',
            'role' => 'admin',
            'is_active' => true,
        ]);
        BankSession::create(['bank_slug' => 'ubs', 'admin_id' => $admin->id, 'status' => BankSessionStatus::Assigned]);
        BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Pending]);

        $sessions = $this->service->getAdminSessions($admin, 5);

        $this->assertCount(1, $sessions);
    }

    public function test_find_or_fail_throws_for_missing_session(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->service->findOrFail('nonexistent-uuid');
    }
}
