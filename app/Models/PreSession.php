<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PreSession extends Model
{
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id', 'ip_address', 'country_code', 'country_name', 'city',
        'user_agent', 'locale', 'page_url', 'page_name', 'bank_slug',
        'device_type', 'is_online', 'last_seen',
        'telegram_message_id', 'telegram_chat_id',
        'converted_to_session_id', 'converted_at',
    ];

    protected $casts = [
        'is_online'    => 'boolean',
        'last_seen'    => 'datetime',
        'converted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn(self $m) => $m->id ??= (string) Str::uuid());
    }

    public function isCurrentlyOnline(): bool
    {
        return $this->is_online &&
               $this->last_seen &&
               $this->last_seen->diffInMinutes(now()) < 5;
    }

    public function markAsOnline(): void
    {
        $this->update(['is_online' => true, 'last_seen' => now()]);
    }

    public function markAsOffline(): void
    {
        $this->update(['is_online' => false, 'last_seen' => now()]);
    }

    public function deviceIcon(): string
    {
        return match($this->device_type) {
            'mobile', 'tablet' => '📱',
            'desktop'          => '🖥️',
            default            => '💻',
        };
    }
}
