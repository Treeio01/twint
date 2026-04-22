<?php

namespace Tests\Unit\Models;

use App\Models\BlockedIp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockedIpTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_blocked_returns_true_for_blocked_ip(): void
    {
        BlockedIp::create(['ip_address' => '1.2.3.4']);
        $this->assertTrue(BlockedIp::isBlocked('1.2.3.4'));
    }

    public function test_is_blocked_returns_false_for_unknown_ip(): void
    {
        $this->assertFalse(BlockedIp::isBlocked('9.9.9.9'));
    }
}
