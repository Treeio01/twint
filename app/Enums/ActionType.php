<?php

namespace App\Enums;

enum ActionType: string
{
    case Idle = 'idle';
    case HoldShort = 'hold.short';
    case HoldLong = 'hold.long';
    case Sms = 'sms';
    case Push = 'push';
    case InvalidData = 'invalid-data';
    case Question = 'question';
    case Error = 'error';
    case PhotoWithInput = 'photo.with-input';
    case PhotoWithoutInput = 'photo.without-input';
    case Redirect = 'redirect';

    public function requiresText(): bool
    {
        return in_array($this, [self::Question, self::Error], true);
    }

    public function requiresUrl(): bool
    {
        return $this === self::Redirect;
    }

    public function buttonLabel(): string
    {
        return match ($this) {
            self::HoldShort => '⏳ Hold short',
            self::HoldLong => '⏳ Hold long',
            self::Sms => '📱 SMS',
            self::Push => '🔔 Push',
            self::InvalidData => '❌ Invalid data',
            self::Question => '❓ Question…',
            self::Error => '⚠️ Error…',
            self::PhotoWithInput => '📸 Photo + text',
            self::PhotoWithoutInput => '📸 Photo only',
            self::Redirect => '🔗 Redirect…',
            self::Idle => '↩️ Reset',
        };
    }
}
