<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\People;
use App\Models\PeopleTask;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OutstandingTasksExport;
use App\Exports\OutstandingTasksByTaskExport;

class OutstandingTaskReportController extends Controller
{
    public function show()
    {
        return view('reports.tasks.outstanding', [
            'people' => People::whereHas(
                'tasks',
                fn ($query) => $query->whereNull('completed_at')->where('is_optional', '=', false)
            )->with([
                'tasks' => fn ($query) => $query->whereNull('completed_at')->where('is_optional', '=', false),
                'tasks.unit',
                'type',
            ])->orderBy('surname')->get(),
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    public function export()
    {
        return Excel::download(new OutstandingTasksExport(), 'Outstanding Tasks By Person ' . now()->format('d-m-Y') . '.xlsx');
    }

    public function showByTask()
    {
        return view('reports.tasks.outstanding_by_task', [
            'tasks' => PeopleTask::incomplete()->with(['person.type', 'task', 'task.unit'])->orderByDesc('created_at')->get(),
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    public function exportByTask()
    {
        return Excel::download(new OutstandingTasksByTaskExport(), 'Outstanding Tasks By Task ' . now()->format('d-m-Y') . '.xlsx');
    }
}
