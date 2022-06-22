<?php

namespace App\Stats;

use App\Models\People;
use App\Models\DailyStat;
use App\Models\PeopleType;

class DailyStatsRecorder
{
    public function record()
    {
        $dailyStat = DailyStat::firstOrNew(['date' => now()]);
        $people = People::current()->with('type')->get();
        $dailyStat->total_count = $people->count();
        $dailyStat->academics_count = $people->filter(fn ($person) => $person->type?->name == PeopleType::ACADEMIC)->count();
        $dailyStat->phd_students_count = $people->filter(fn ($person) => $person->type?->name == PeopleType::PHD)->count();
        $dailyStat->mpas_count = $people->filter(fn ($person) => $person->type?->name == PeopleType::MPA)->count();
        $dailyStat->technicians_count = $people->filter(fn ($person) => $person->type?->name == PeopleType::TECHNICAL)->count();
        $dailyStat->save();
    }
}
