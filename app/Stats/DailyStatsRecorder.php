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
        $dailyStat->total_count = People::current()->count();
        $dailyStat->academics_count = People::current()->where('type', '=', PeopleType::ACADEMIC)->count();
        $dailyStat->phd_students_count = People::current()->where('type', '=', PeopleType::PHD_STUDENT)->count();
        $dailyStat->mpas_count = People::current()->where('type', '=', PeopleType::MPA)->count();
        $dailyStat->technicians_count = People::current()->where('type', '=', PeopleType::TECHNICIAN)->count();
        $dailyStat->save();
    }
}
