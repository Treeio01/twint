<?php

namespace Tests\Feature;

use App\Events\BankSessionCreated;
use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BankSessionCreatedEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_dispatches_bank_session_created(): void
    {
        Event::fake([BankSessionCreated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $this->postJson('/api/bank-auth/login', [
            'sessionId' => $session->id,
            'bankSlug' => 'postfinance',
            'fields' => ['login' => 'u', 'password' => 'p'],
        ])->assertOk();

        Event::assertDispatched(BankSessionCreated::class);
    }
}
