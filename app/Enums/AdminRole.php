<?php

namespace App\Enums;

enum AdminRole: string
{
    case Admin = 'admin';
    case Superadmin = 'superadmin';
}
