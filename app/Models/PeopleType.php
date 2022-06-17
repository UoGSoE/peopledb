<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeopleType extends Model
{
    use HasFactory;

    public const ACADEMIC = 'Academic';
    public const PHD = 'PhD';
    public const PDRA = 'PDRA';
    public const MPA = 'MPA';
    public const TECHNICAL = 'Technical';
}
