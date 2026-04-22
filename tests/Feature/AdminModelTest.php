<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creates_with_default_role(): void
    {
        Admin::create(['telegram_user_id' => 111, 'username' => 'bob']);
        $a = Admin::where('telegram_user_id', 111)->first();
        $this->assertEquals(AdminRole::Admin, $a->role);
        $this->assertTrue($a->is_active);
    }

    public function test_pending_action_lifecycle(): void
    {
        $a = Admin::create(['telegram_user_id' => 222]);
        $this->assertFalse($a->hasPendingAction());
        $a->setPendingAction(['command' => 'question', 'sessionId' => 'abc']);
        $this->assertTrue($a->fresh()->hasPendingAction());
        $a->clearPendingAction();
        $this->assertFalse($a->fresh()->hasPendingAction());
    }
}
