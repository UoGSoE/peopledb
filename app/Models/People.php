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

    protected $fillable = [
        'end_at',
    ];

    protected $appends = [
        'full_name',
    ];

    public function reportsTo()
    {
        return $this->belongsTo(People::class, 'reports_to');
    }

    public function reportees()
    {
        return $this->hasMany(People::class, 'reports_to');
    }

    public function scopeCurrent($query)
    {
        return $query->where('end_at', '>=', now())->where('start_at', '<=', now());
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', '=', $type);
    }

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', '=', $group);
    }

    public function scopeRecentlyArrived($query)
    {
        return $query->where('start_at', '<=', now())
            ->where('start_at', '>', now()->subDays(config('peopledb.recent_days_arriving')));
    }

    public function scopeRecentlyLeft($query)
    {
        return $query->where('end_at', '<=', now())
            ->where('end_at', '>', now()->subDays(config('peopledb.recent_days_leaving')));
    }

    public function scopeArrivingSoon($query, $includingDaysPast = 0)
    {
        return $query->where('start_at', '>', now()->subDays($includingDaysPast))
            ->where('start_at', '<', now()->addDays(config('peopledb.recent_days_arriving')));
    }

    public function scopeLeavingSoon($query, $includingDaysPast = 0)
    {
        return $query->where('end_at', '>', now()->subDays($includingDaysPast))
            ->where('end_at', '<', now()->addDays(config('peopledb.recent_days_leaving')));
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->forenames . ' ' . $this->surname,
        );
    }

    public function getArrivalsAndDepartures(): ArrivalsDeparturesDto
    {
        $recentlyArrived = People::recentlyArrived()
            ->orderBy('start_at')
            ->with('reportsTo')
            ->get();
        $recentlyLeft = People::recentlyLeft()
            ->orderBy('end_at')
            ->with('reportsTo')
            ->get();
        $upcomingArrivals = People::arrivingSoon()
            ->orderBy('start_at')
            ->with('reportsTo')
            ->get();
        $upcomingDepartures = People::leavingSoon()
            ->orderBy('end_at')
            ->with('reportsTo')
            ->get();

        return new ArrivalsDeparturesDto(
            $upcomingArrivals,
            $upcomingDepartures,
            $recentlyArrived,
            $recentlyLeft,
        );
    }
}
