<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\People;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskCompleteController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer|exists:tasks,id',
            'person_guid' => 'required|string|exists:people,username',
            'completer_guid' => 'required|string|exists:users,username',
            'notes' => 'sometimes|string|max:500',
        ]);

        $person = People::where('username', '=', $request->person_guid)->firstOrFail();
        $task = $person->tasks()->where('task_id', '=', $request->task_id)->firstOrFail()->pivot;
        $user = User::where('username', '=', $request->completer_guid)->firstOrFail();

        $task->completed_by = $user->id;
        $task->completed_at = now();
        if ($request->notes) {
            $task->notes = $request->notes . "\n" . (string) $task->notes;
        }
        $task->save();

        return response()->json([
            'message' => 'Task marked as complete',
            'data' => [],
        ]);
    }
}
