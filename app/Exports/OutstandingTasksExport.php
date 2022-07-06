<?php

namespace App\Exports;

use App\Models\People;
use Maatwebsite\Excel\Concerns\FromCollection;

class OutstandingTasksExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return People::whereHas(
            'tasks',
            fn ($query) => $query->whereNull('completed_at')->where('is_optional', '=', false)
        )->with([
            'tasks' => fn ($query) => $query->whereNull('completed_at')->where('is_optional', '=', false),
            'tasks.unit',
            'type',
        ])->orderBy('surname')->get()->flatMap(function ($person) {
            return $person->tasks->map(function ($task) use ($person) {
                return [
                    'username' => $person->username,
                    'person' => $person->full_name,
                    'email' => $person->email,
                    'task' => $task->description,
                    'unit' => $task->unit->name,
                    'created' => $task->pivot->created_at->format('d/m/Y'),
                ];
            });
        });
    }
}
