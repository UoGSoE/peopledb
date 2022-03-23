<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show()
    {
        return view('home', [
            'recentishArrivals' => People::whereBetween('start_at', [now()->subDays(14), now()->addDays(28)])->orderBy('start_at')->get(),
            'recentishDepartures' => People::whereBetween('end_at', [now()->subDays(14), now()->addDays(28)])->orderBy('end_at')->get(),
        ]);
    }
}
