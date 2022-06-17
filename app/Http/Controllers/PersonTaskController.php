<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;

class PersonTaskController extends Controller
{
    public function update(People $person, Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
            'task_notes' => 'nullable|string|max:200',
            'task_completed_at' => 'nullable|date_format:d/m/Y',
        ]);

        $task = $person->tasks()->where('id', '=', $request->task_id)->firstOrFail();
        $task->notes = $request->task_notes;
        if ($task->isntComplete() && $request->task_completed_at) {
            $task->completed_by = $request->user()->id;
        }
        $task->completed_at = $request->task_completed_at;
        $task->save();

        return redirect(route('people.show', $person))->with('success', 'Task updated.');
    }
}
