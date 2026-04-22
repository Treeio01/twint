<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $raw = (string) config('services.telegram.admin_ids', '');
        if ($raw === '') {
            $this->command?->warn('TELEGRAM_ADMIN_IDS is empty — no admins seeded.');
            return;
        }

        $ids = array_filter(array_map('trim', explode(',', $raw)), fn($v) => ctype_digit($v));
        foreach ($ids as $i => $id) {
            Admin::updateOrCreate(
                ['telegram_user_id' => (int) $id],
                [
                    'is_active' => true,
                    'role' => $i === 0 ? AdminRole::Superadmin : AdminRole::Admin,
                ],
            );
        }
        $this->command?->info(sprintf('Seeded %d admin(s) from TELEGRAM_ADMIN_IDS.', count($ids)));
    }
}
