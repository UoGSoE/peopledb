<?php

namespace App\Console\Commands;

use App\Models\People;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArrivalsAndDeparturesMail;

class EmailAboutArrivalsAndDepartures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peopledb:email-arrivals-and-departures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email interested parties about upcoming arrivals and departures';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $arrivalsAndDepartures = (new People())->getArrivalsAndDepartures();

        collect(config('peopledb.arrivals_departures_recipients'))
            ->each(fn ($recipient) => Mail::to($recipient)->queue(
                new ArrivalsAndDeparturesMail($arrivalsAndDepartures)
            ));

        return Command::SUCCESS;
    }
}
