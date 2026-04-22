<?php

namespace App\Events;

use App\Models\PreSession;
use Illuminate\Foundation\Events\Dispatchable;

class PreSessionCreated
{
    use Dispatchable;

    public function __construct(public readonly PreSession $preSession) {}
}
