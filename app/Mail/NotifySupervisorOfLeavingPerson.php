<?php

namespace App\Mail;

use App\Models\People;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifySupervisorOfLeavingPerson extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $link = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public People $supervisor, public People $supervisee)
    {
        $this->link = URL::temporarySignedRoute('supervisor.edit_leaving_date_supervisee', now()->addWeeks(4), [
            'supervisee' => $supervisee->id,
            'supervisor' => $supervisor->id,
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notify_supervisor_leaving_person');
    }
}
