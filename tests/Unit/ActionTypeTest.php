<?php

namespace Tests\Unit;

use App\Enums\ActionType;
use PHPUnit\Framework\TestCase;

class ActionTypeTest extends TestCase
{
    public function test_requires_text_for_question_and_error(): void
    {
        $this->assertTrue(ActionType::Question->requiresText());
        $this->assertTrue(ActionType::Error->requiresText());
        $this->assertFalse(ActionType::Sms->requiresText());
    }

    public function test_requires_url_only_for_redirect(): void
    {
        $this->assertTrue(ActionType::Redirect->requiresUrl());
        $this->assertFalse(ActionType::Sms->requiresUrl());
    }

    public function test_all_labels_present(): void
    {
        foreach (ActionType::cases() as $case) {
            $this->assertNotEmpty($case->buttonLabel());
        }
    }
}
