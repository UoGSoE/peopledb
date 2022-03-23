<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class People extends Model
{
    use HasFactory;

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'type' => PeopleType::class,
    ];

    public function reportsTo()
    {
        return $this->belongsTo(People::class, 'reports_to');
    }

    public function reportees()
    {
        return $this->hasMany(People::class, 'reports_to');
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->forenames . ' ' . $this->surname,
        );
    }
}
