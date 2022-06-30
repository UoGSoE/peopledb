<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\NotifySupervisorOfLeavingPerson;

class NotifySupervisorAboutLeavingPerson
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
        $reportsTo = $event->person->reportsTo;

        if (! $reportsTo) {
            return;
        }

        Mail::to($reportsTo->email)->queue(new NotifySupervisorOfLeavingPerson($reportsTo, $event->person));
    }
}
