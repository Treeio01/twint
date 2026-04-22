<?php

namespace Tests\Feature;

use App\Events\BankSessionUpdated;
use App\Models\Admin;
use App\Models\BankSession;
use App\Telegram\Handlers\MessageHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Mockery;
use SergiX44\Nutgram\Nutgram;
use Tests\TestCase;

class MessageHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_text_answers_pending_question(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $admin = Admin::create(['telegram_user_id' => 1]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);
        $admin->setPendingAction(['sessionId' => $session->id, 'actionType' => 'question']);

        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('get')->with('admin')->andReturn($admin);
        $bot->shouldReceive('sendMessage')->once();

        (new MessageHandler())->handle($bot, 'Enter your code from SMS');

        $this->assertEquals(['type' => 'question', 'text' => 'Enter your code from SMS'], $session->fresh()->action_type);
        $this->assertFalse($admin->fresh()->hasPendingAction());
        Event::assertDispatched(BankSessionUpdated::class);
    }

    public function test_text_for_redirect_goes_to_url_field(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $admin = Admin::create(['telegram_user_id' => 1]);
        $session = BankSession::create(['bank_slug' => 'ubs']);
        $admin->setPendingAction(['sessionId' => $session->id, 'actionType' => 'redirect']);

        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('get')->with('admin')->andReturn($admin);
        $bot->shouldReceive('sendMessage')->once();

        (new MessageHandler())->handle($bot, 'https://ubs.com/finish');

        $this->assertEquals(['type' => 'redirect', 'url' => 'https://ubs.com/finish'], $session->fresh()->action_type);
    }

    public function test_no_pending_action_is_a_noop(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $admin = Admin::create(['telegram_user_id' => 1]);
        $bot = Mockery::mock(Nutgram::class);
        $bot->shouldReceive('get')->with('admin')->andReturn($admin);

        (new MessageHandler())->handle($bot, 'random message');

        Event::assertNotDispatched(BankSessionUpdated::class);
    }
}
