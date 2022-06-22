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

        $academicType = PeopleType::where('name', '=', PeopleType::ACADEMIC)->firstOrFail();
        $phdType = PeopleType::where('name', '=', PeopleType::PHD)->firstOrFail();
        $mpaType = PeopleType::where('name', '=', PeopleType::MPA)->firstOrFail();
        $technicalType = PeopleType::where('name', '=', PeopleType::TECHNICAL)->firstOrFail();

        $stats = [
            'total_count' => People::current()->count(),
            'total_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->count(),
            'total_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->count(),
            'academics_count' => People::current()->where('people_type_id', '=', $academicType->id)->count(),
            'academics_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->where('people_type_id', '=', $academicType->id)->count(),
            'academics_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->where('people_type_id', '=', $academicType->id)->count(),
            'phds_count' => People::current()->where('people_type_id', '=', $phdType->id)->count(),
            'phds_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->where('people_type_id', '=', $phdType->id)->count(),
            'phds_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->where('people_type_id', '=', $phdType->id)->count(),
            'mpas_count' => People::current()->where('people_type_id', '=', $mpaType->id)->count(),
            'mpas_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->where('people_type_id', '=', $mpaType->id)->count(),
            'mpas_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->where('people_type_id', '=', $mpaType->id)->count(),
            'technicians_count' => People::current()->where('people_type_id', '=', $technicalType->id)->count(),
            'technicians_leaving_count' => People::leavingSoon(includingDaysPast: $includeDaysPast)->where('people_type_id', '=', $technicalType->id)->count(),
            'technicians_arriving_count' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->where('people_type_id', '=', $technicalType->id)->count(),
        ];

        return view('home', [
            'recentishArrivals' => People::arrivingSoon(includingDaysPast: $includeDaysPast)->orderBy('start_at')->get(),
            'recentishDepartures' => People::leavingSoon(includingDaysPast: $includeDaysPast)->orderBy('end_at')->get(),
            'stats' => $stats,
        ]);
    }
}
