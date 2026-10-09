<?php

namespace App\Domain\Scheduling\Enums;

enum Scope: string
{
    case ONCE = 'once';
    case ONWARDS = 'onwards';
}
