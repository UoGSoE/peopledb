<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\PeopleType;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show()
    {
        $includeDaysPast = config('peopledb.recentish_days_fudge_factor', 14);

        $stats = [
            'total_count' => People::current()->count(),
            'total_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->count(),
            'total_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->count(),
            'academics_count' => People::current()->where('type', '=', PeopleType::ACADEMIC)->count(),
            'academics_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->where('type', '=', PeopleType::ACADEMIC)->count(),
            'academics_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->where('type', '=', PeopleType::ACADEMIC)->count(),
            'phds_count' => People::current()->where('type', '=', PeopleType::PHD_STUDENT)->count(),
            'phds_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->where('type', '=', PeopleType::PHD_STUDENT)->count(),
            'phds_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->where('type', '=', PeopleType::PHD_STUDENT)->count(),
            'mpas_count' => People::current()->where('type', '=', PeopleType::MPA)->count(),
            'mpas_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->where('type', '=', PeopleType::MPA)->count(),
            'mpas_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->where('type', '=', PeopleType::MPA)->count(),
            'technicians_count' => People::current()->where('type', '=', PeopleType::TECHNICIAN)->count(),
            'technicians_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->where('type', '=', PeopleType::TECHNICIAN)->count(),
            'technicians_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->where('type', '=', PeopleType::TECHNICIAN)->count(),
        ];

        return view('home', [
            'recentishArrivals' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->orderBy('start_at')->get(),
            'recentishDepartures' => People::leavingSoon(includingDaysPast: $includeDaysPast)->orderBy('end_at')->get(),
            'stats' => $stats,
        ]);
    }
}
