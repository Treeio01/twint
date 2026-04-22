<?php

namespace Tests\Unit\Services;

use App\Services\CloudflareService;
use Tests\TestCase;

class CloudflareServiceTest extends TestCase
{
    public function test_not_configured_when_credentials_empty(): void
    {
        config(['services.cloudflare.api_token' => '']);
        config(['services.cloudflare.api_email' => '']);
        config(['services.cloudflare.api_key' => '']);
        $svc = new CloudflareService();
        $this->assertFalse($svc->isConfigured());
    }

    public function test_configured_when_token_set(): void
    {
        config(['services.cloudflare.api_token' => 'fake-token']);
        $svc = new CloudflareService();
        $this->assertTrue($svc->isConfigured());
    }
}
