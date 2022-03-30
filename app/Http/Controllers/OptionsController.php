<?php

namespace App\Http\Controllers;

use App\Models\SiteOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OptionsController extends Controller
{
    public function edit()
    {
        return view('options.edit', [
            'recent_days_arriving' => SiteOption::findByKey('recent_days_arriving', 20),
            'recent_days_leaving' => SiteOption::findByKey('recent_days_leaving', 20),
            'arrivals_departures_recipients' => SiteOption::findByKey('arrivals_departures_recipients', ''),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'recent_days_arriving' => 'required|integer|min:1',
            'recent_days_leaving' => 'required|integer|min:1',
            'arrivals_departures_recipients' => 'required|string',
        ]);
        $emails = explode(',', $request->arrivals_departures_recipients);
        foreach ($emails as $email) {
            Validator::make(['email' => trim($email)], ['email' => 'email'])->validate();
        }

        SiteOption::updateOrCreate(['key' => 'recent_days_arriving'], ['value' => $request->recent_days_arriving]);
        SiteOption::updateOrCreate(['key' => 'recent_days_leaving'], ['value' => $request->recent_days_leaving]);
        SiteOption::updateOrCreate(['key' => 'arrivals_departures_recipients'], ['value' => $request->arrivals_departures_recipients]);

        return redirect()->route('options.edit')->with('success', 'Options updated');
    }
}
