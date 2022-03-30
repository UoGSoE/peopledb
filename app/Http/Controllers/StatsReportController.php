<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatsReportController extends Controller
{
    public function show()
    {
        return view('reports.stats');
    }
}
