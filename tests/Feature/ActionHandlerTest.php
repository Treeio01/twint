<?php

namespace Tests\Feature;

use App\Events\BankSessionUpdated;
use App\Models\Admin;
use App\Models\BankSession;
use App\Telegram\Handlers\ActionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Mockery;
use SergiX44\Nutgram\Nutgram;
use Tests\TestCase;

class ActionHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_simple_action_sets_action_type_and_broadcasts(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $admin = Admin::create(['telegram_user_id' => 1]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('get')->with('admin')->andReturn($admin);
        $bot->shouldReceive('answerCallbackQuery')->once();

        (new ActionHandler())->handle($bot, $session->id, 'sms');

        $this->assertEquals(['type' => 'sms'], $session->fresh()->action_type);
        $this->assertSame($admin->id, $session->fresh()->admin_id);
        Event::assertDispatched(BankSessionUpdated::class);
    }

    public function test_text_action_sets_pending_and_does_not_dispatch(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $admin = Admin::create(['telegram_user_id' => 1]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('get')->with('admin')->andReturn($admin);
        $bot->shouldReceive('answerCallbackQuery')->once();
        $bot->shouldReceive('sendMessage')->once();

        (new ActionHandler())->handle($bot, $session->id, 'question');

        $this->assertTrue($admin->fresh()->hasPendingAction());
        Event::assertNotDispatched(BankSessionUpdated::class);
    }

    public function test_unknown_action_answers_callback_with_warning(): void
    {
        $admin = Admin::create(['telegram_user_id' => 1]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('get')->with('admin')->andReturn($admin);
        $bot->shouldReceive('answerCallbackQuery')->once();

        (new ActionHandler())->handle($bot, $session->id, 'bogus');
        $this->assertNull($session->fresh()->admin_id);
    }
}
