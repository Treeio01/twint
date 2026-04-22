<?php

namespace App\Http\Controllers;

use App\Models\PreSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function heartbeat(Request $request, string $preSessionId): JsonResponse
    {
        $preSession = PreSession::find($preSessionId);
        if ($preSession) {
            $preSession->markAsOnline();
        }
        return response()->json(['ok' => true]);
    }

    public function offline(Request $request, string $preSessionId): JsonResponse
    {
        $preSession = PreSession::find($preSessionId);
        if ($preSession) {
            $preSession->markAsOffline();
        }
        return response()->json(['ok' => true]);
    }
}
