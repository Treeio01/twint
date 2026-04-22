<?php

namespace Tests\Feature;

use App\Models\BlockedIp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockedIpMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_ip_gets_403(): void
    {
        BlockedIp::create(['ip_address' => '1.2.3.4']);
        $response = $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
                         ->get('/postfinance');
        $response->assertStatus(403);
    }

    public function test_non_blocked_ip_passes(): void
    {
        $response = $this->withServerVariables(['REMOTE_ADDR' => '9.9.9.9'])
                         ->get('/postfinance');
        $response->assertStatus(200);
    }
}
