<?php

namespace App\Models;

use App\Enums\AdminRole;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'telegram_user_id',
        'username',
        'role',
        'is_active',
        'pending_action',
    ];

    protected $casts = [
        'role'             => AdminRole::class,
        'is_active'        => 'boolean',
        'pending_action'   => 'array',
        'telegram_user_id' => 'integer',
    ];

    public function canAddAdmins(): bool
    {
        return $this->role?->canAddAdmins() ?? false;
    }

    public function hasPendingAction(): bool
    {
        return !empty($this->pending_action);
    }

    public function setPendingAction(array $payload): void
    {
        $this->pending_action = $payload;
        $this->save();
    }

    public function clearPendingAction(): void
    {
        $this->pending_action = null;
        $this->save();
    }
}
