<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\PeopleTask;
use Illuminate\Http\Request;

class OutstandingTaskReportController extends Controller
{
    public function show()
    {
        return view('reports.tasks.outstanding', [
            'people' => People::whereHas(
                'tasks',
                fn ($query) => $query->whereNull('completed_at')->where('is_optional', '=', false)
            )->with([
                'tasks.unit',
                'type',
            ])->orderBy('surname')->get(),
        ]);
    }

    public function showByTask()
    {
        return view('reports.tasks.outstanding_by_task', [
            'tasks' => PeopleTask::incomplete()->with(['person.type', 'task', 'task.unit'])->orderByDesc('created_at')->get(),
        ]);
    }
}
