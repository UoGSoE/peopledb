<?php

namespace App\Models;

use App\Models\User;
use App\Models\UnitEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function emails()
    {
        return $this->hasMany(UnitEmail::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function onboardingTasks()
    {
        return $this->hasMany(Task::class)->where('is_onboarding', true);
    }

    public function leavingTasks()
    {
        return $this->hasMany(Task::class)->where('is_onboarding', false);
    }

    public function getCssClassTagColourAttribute(): string
    {
        return match ($this->name) {
            'IT' => 'is-danger',
            'Facilities' => 'is-warning',
            'Teaching' => 'is-info',
            'Research Office' => 'is-link',
            'School Admin' => 'is-primary',
            default => '',
        };
    }
}
