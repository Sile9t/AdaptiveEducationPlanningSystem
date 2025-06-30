<?php

namespace App\Models;

enum PriorityStatus: string
{
    case Passed = "Просрочен";
    case Expiring = "Истекает";
    case Control = "Контроль";
    case Active = "Активен";
}
