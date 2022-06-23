<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_optional' => 'boolean',
        'is_onboarding' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function peopleTypes()
    {
        return $this->belongsToMany(PeopleType::class);
    }

    public static function makeDefault(array $overrides = []): static
    {
        $defaults = [
            'id' => 0,
            'is_optional' => false,
            'is_onboarding' => true,
            'is_active' => true,
        ];
        return new static(array_merge($defaults, $overrides));
    }

    public function isOptional(): bool
    {
        return (bool) $this->is_optional;
    }

    public function isntOptional(): bool
    {
        return ! $this->isOptional();
    }

    public function isOnboarding(): bool
    {
        return (bool) $this->is_onboarding;
    }

    public function isLeaving(): bool
    {
        return ! $this->isOnboarding();
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function isntActive(): bool
    {
        return ! $this->isActive();
    }

    public function appliesTo(PeopleType $type): bool
    {
        return $this->peopleTypes->contains($type);
    }

    public function getCssClassTagColourAttribute(): string
    {
        return match ($this->unit->name) {
            'IT' => 'is-danger',
            'Facilities' => 'is-warning',
            'Teaching' => 'is-info',
            'Research Office' => 'is-link',
            'School Admin' => 'is-primary',
            default => '',
        };
    }
}
