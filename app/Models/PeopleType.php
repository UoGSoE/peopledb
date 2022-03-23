<?php

namespace App\Models;

use ArchTech\Enums\Values;

enum PeopleType: string
{
    use Values;

    case ACADEMIC = 'Academic';
    case PHD_STUDENT = 'PhD Student';
    case RA = 'RA';
    case TECHNICIAN = 'Technician';
    case MPA = 'MPA';
}
