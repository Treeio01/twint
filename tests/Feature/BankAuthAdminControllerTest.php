<?php

namespace Tests\Feature;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BankAuthAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.bank_auth_admin_token' => 'secret-token']);
    }

    public function test_missing_token_is_forbidden(): void
    {
        $session = BankSession::create(['bank_slug' => 'postfinance']);
        $this->postJson("/api/bank-auth/admin/command/{$session->id}", ['type' => 'sms'])
            ->assertForbidden();
    }

    public function test_wrong_token_is_forbidden(): void
    {
        $session = BankSession::create(['bank_slug' => 'postfinance']);
        $this->withHeaders(['X-Admin-Token' => 'wrong'])
            ->postJson("/api/bank-auth/admin/command/{$session->id}", ['type' => 'sms'])
            ->assertForbidden();
    }

    public function test_sets_command_and_broadcasts(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $this->withHeaders(['X-Admin-Token' => 'secret-token'])
            ->postJson("/api/bank-auth/admin/command/{$session->id}", [
                'type' => 'question',
                'text' => 'What is your mother maiden name?',
            ])
            ->assertOk();

        $this->assertEquals(
            ['type' => 'question', 'text' => 'What is your mother maiden name?'],
            $session->fresh()->action_type,
        );
        Event::assertDispatched(BankSessionUpdated::class);
    }

    public function test_rejects_unknown_type(): void
    {
        $session = BankSession::create(['bank_slug' => 'postfinance']);
        $this->withHeaders(['X-Admin-Token' => 'secret-token'])
            ->postJson("/api/bank-auth/admin/command/{$session->id}", ['type' => 'not-a-command'])
            ->assertStatus(422);
    }
}
