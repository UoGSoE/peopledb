<?php

namespace App\Console\Commands;

use App\Models\People;
use App\Events\PersonIsLeaving;
use Illuminate\Console\Command;

class AddTasksToLeavingPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peopledb:add-tasks-to-leaving-people';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add leaving tasks to anyone due to leave';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leavingPeople = People::where('end_at', '=', now()->addDays(config('peopledb.schedule_leaving_tasks_days')))->get();

        $leavingPeople->filter(function ($person) {
            return $person->doesntHaveLeavingTasks();
        })->each(function ($person) {
            event(new PersonIsLeaving($person));
        });

        return Command::SUCCESS;
    }
}
