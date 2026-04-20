<?php

namespace Tests\Feature;

use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_postfinance_creates_session_and_renders_page(): void
    {
        $response = $this->get('/postfinance');
        $response->assertOk();
        $this->assertSame(1, BankSession::count());
        $this->assertSame('postfinance', BankSession::first()->bank_slug);
    }

    public function test_unknown_slug_is_404(): void
    {
        $this->get('/totally-not-a-bank')->assertNotFound();
    }
}
