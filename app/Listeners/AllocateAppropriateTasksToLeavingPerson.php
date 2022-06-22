<?php

namespace App\Listeners;

use App\Models\Task;
use App\Models\PeopleTypeTask;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AllocateAppropriateTasksToLeavingPerson
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $person = $event->person;

        if ($person->hasLeavingTasks()) {
            return;
        }

        $applicableTasks = PeopleTypeTask::where('people_type_id', $person->people_type_id)->get()->pluck('task_id');
        $leavingTasks = Task::findMany($applicableTasks)->filter(fn ($task) => $task->isLeaving())->pluck('id');
        $person->tasks()->syncWithoutDetaching($leavingTasks);
    }
}
