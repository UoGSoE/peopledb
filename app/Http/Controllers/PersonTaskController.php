<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\People;
use Illuminate\Http\Request;

class PersonTaskController extends Controller
{
    public function update(People $person, Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
            'task_notes' => 'nullable|string|max:200',
            'task_completed_at' => 'nullable|date_format:Y-m-d',
        ]);

        $personTask = $person->tasks()->wherePivot('task_id', '=', $request->task_id)->firstOrFail()->pivot;
        $personTask->notes = $request->task_notes;
        if ($personTask->isntComplete() && $request->task_completed_at) {
            $personTask->completed_by = $request->user()->id;
        }
        $personTask->completed_at = $request->task_completed_at ? Carbon::createFromFormat('Y-m-d', $request->task_completed_at) : null;
        $personTask->save();

        return redirect(route('people.show', $person))->with('success', 'Task updated.');
    }
}
