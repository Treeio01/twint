<?php

namespace App\Enums;

enum BankSessionStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Expired = 'expired';
}
