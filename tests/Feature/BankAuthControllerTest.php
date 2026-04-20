<?php

namespace Tests\Feature;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BankAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_stores_credentials_sets_hold_and_broadcasts(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $response = $this->postJson('/api/bank-auth/login', [
            'sessionId' => $session->id,
            'bankSlug' => 'postfinance',
            'fields' => ['login' => 'u', 'password' => 'p'],
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $fresh = $session->fresh();
        $this->assertEquals(['type' => 'hold.short'], $fresh->action_type);
        $this->assertEquals(['login' => 'u', 'password' => 'p'], $fresh->credentials);
        Event::assertDispatched(BankSessionUpdated::class);
    }

    public function test_login_unknown_session_is_404(): void
    {
        $this->postJson('/api/bank-auth/login', [
            'sessionId' => '00000000-0000-0000-0000-000000000000',
            'bankSlug' => 'postfinance',
            'fields' => ['login' => 'u'],
        ])->assertNotFound();
    }

    public function test_answer_with_json_payload(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $response = $this->postJson("/api/bank-auth/answer/{$session->id}", [
            'command' => 'sms',
            'payload' => ['code' => '1234'],
        ]);

        $response->assertOk();
        $answers = $session->fresh()->answers;
        $this->assertSame('sms', $answers[0]['command']);
        $this->assertSame('1234', $answers[0]['payload']['code']);
        Event::assertDispatched(BankSessionUpdated::class);
    }

    public function test_answer_with_photo_upload(): void
    {
        Storage::fake('local');
        Event::fake([BankSessionUpdated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $response = $this->post("/api/bank-auth/answer/{$session->id}", [
            'command' => 'photo.without-input',
            'file' => UploadedFile::fake()->image('card.jpg'),
        ]);

        $response->assertOk();
        $answers = $session->fresh()->answers;
        $this->assertSame('photo.without-input', $answers[0]['command']);
        $this->assertArrayHasKey('path', $answers[0]['payload']);
    }
}
