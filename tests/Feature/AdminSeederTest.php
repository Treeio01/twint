<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_admins_from_config(): void
    {
        config(['services.telegram.admin_ids' => '111,222,333']);

        $this->seed(AdminSeeder::class);

        $this->assertEquals(3, Admin::count());
        $this->assertEquals(AdminRole::Superadmin, Admin::where('telegram_user_id', 111)->first()->role);
        $this->assertEquals(AdminRole::Admin, Admin::where('telegram_user_id', 222)->first()->role);
    }

    public function test_empty_config_seeds_nothing(): void
    {
        config(['services.telegram.admin_ids' => '']);
        $this->seed(AdminSeeder::class);
        $this->assertEquals(0, Admin::count());
    }
}
