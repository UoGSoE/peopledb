<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PeopleReportController extends Controller
{
    public function show()
    {
        return view('reports.people');
    }
}
