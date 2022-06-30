<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AlterLeavingDateController extends Controller
{
    public function edit(People $supervisee, People $supervisor)
    {
        if (! request()->hasValidSignature()) {
            abort(401);
        }

        abort_unless($supervisee->reportsTo?->is($supervisor), 401, 'You are not listed as the supervisor of this person.');

        return view('supervisor.edit_supervisee_leaving_date', [
            'supervisee' => $supervisee,
            'supervisor' => $supervisor,
        ]);
    }

    public function update(People $supervisee, People $supervisor, Request $request)
    {
        if (! request()->hasValidSignature()) {
            abort(401);
        }

        abort_unless($supervisee->reportsTo?->is($supervisor), 401, 'You are not listed as the supervisor of this person.');

        $request->validate([
            'end_at' => 'required|date_format:Y-m-d',
        ]);

        $supervisee->end_at = Carbon::createFromFormat('Y-m-d', $request->end_at);
        $supervisee->save();

        return redirect(
            URL::temporarySignedRoute(
                'supervisor.edit_leaving_date_supervisee',
                now()->addWeeks(4),
                [
                    'supervisee' => $supervisee->id,
                    'supervisor' => $supervisor->id,
                ]
            )
        )->with('success', 'Leaving date updated!');
    }
}
