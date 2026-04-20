<?php

namespace Tests\Feature;

use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BankSessionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_session_sets_uuid_and_idle_command(): void
    {
        $s = BankSession::create(['bank_slug' => 'postfinance']);
        $this->assertNotEmpty($s->id);
        $this->assertEquals(['type' => 'idle'], $s->action_type);
        $this->assertEquals([], $s->answers);
    }

    public function test_credentials_are_encrypted_at_rest(): void
    {
        $s = BankSession::create([
            'bank_slug' => 'postfinance',
            'credentials' => ['login' => 'u', 'password' => 'supersecret'],
        ]);
        $raw = DB::table('bank_sessions')->where('id', $s->id)->value('credentials');
        $this->assertStringNotContainsString('supersecret', (string) $raw);
        $this->assertEquals(
            ['login' => 'u', 'password' => 'supersecret'],
            $s->fresh()->credentials,
        );
    }

    public function test_push_answer_appends(): void
    {
        $s = BankSession::create(['bank_slug' => 'postfinance']);
        $s->pushAnswer(['command' => 'sms', 'payload' => ['code' => '1234']]);
        $s->pushAnswer(['command' => 'idle', 'payload' => ['login' => 'u']]);
        $this->assertCount(2, $s->fresh()->answers);
    }
}
