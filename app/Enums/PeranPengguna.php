<?php

namespace App\Enums;

enum PeranPengguna: string
{
    case Admin = 'Admin';
    case Operator = 'Operator';
    case Member = 'Member';
}
