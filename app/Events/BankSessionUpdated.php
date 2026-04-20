<?php

namespace App\Events;

use App\Models\BankSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class BankSessionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public readonly BankSession $session)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel("bank-session.{$this->session->id}");
    }

    public function broadcastAs(): string
    {
        return 'BankSessionUpdated';
    }

    public function broadcastWith(): array
    {
        return ['command' => $this->session->action_type];
    }
}
