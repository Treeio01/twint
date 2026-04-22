<?php

namespace App\Enums;

enum BankSessionStatus: string
{
    case Pending   = 'pending';
    case Assigned  = 'assigned';
    case Completed = 'completed';
}
