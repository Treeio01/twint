<?php

namespace App\Http\Controllers;

use App\Models\PreSession;
use Illuminate\Http\Response;

class HeartbeatController extends Controller
{
    public function ping(string $preSessionId): Response
    {
        $preSession = PreSession::find($preSessionId);
        if ($preSession) {
            $preSession->markAsOnline();
        }
        return response()->noContent();
    }

    public function offline(string $preSessionId): Response
    {
        $preSession = PreSession::find($preSessionId);
        if ($preSession) {
            $preSession->markAsOffline();
        }
        return response()->noContent();
    }
}
