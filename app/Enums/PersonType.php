<?php

namespace App\Enums;

enum PersonType: string
{
    case ROBOT = 'robot';
    case HUMAN = 'human';
    case UNKNOWN = 'unknown';
}
