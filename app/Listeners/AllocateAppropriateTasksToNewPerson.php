<?php

namespace App\Listeners;

use App\Models\People;
use App\Models\PeopleTypeTask;
use App\Models\Task;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AllocateAppropriateTasksToNewPerson implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle($event)
    {
        $person = $event->person;
        $applicableTasks = PeopleTypeTask::where('people_type_id', $person->people_type_id)->get();
        $person->tasks()->sync($applicableTasks->pluck('task_id'));
    }
}
