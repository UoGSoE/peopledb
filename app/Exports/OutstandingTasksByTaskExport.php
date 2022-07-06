<?php

namespace App\Exports;

use App\Models\PeopleTask;
use Maatwebsite\Excel\Concerns\FromCollection;

class OutstandingTasksByTaskExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PeopleTask::incomplete()->with(['person', 'person.type', 'task', 'task.unit'])->orderByDesc('created_at')->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'task' => $task->task->description,
                'created' => $task->created_at->format('d/m/Y'),
                'unit' => $task->task->unit->name,
                'person' => $task->person->full_name,
                'email' => $task->person->email,
                'username' => $task->person->username,
            ];
        });
    }
}
