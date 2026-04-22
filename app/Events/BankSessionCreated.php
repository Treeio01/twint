<?php

namespace App\Events;

use App\Models\BankSession;
use Illuminate\Foundation\Events\Dispatchable;

class BankSessionCreated
{
    use Dispatchable;

    public function __construct(public readonly BankSession $session)
    {
    }
}
