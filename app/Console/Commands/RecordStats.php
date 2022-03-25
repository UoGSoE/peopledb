<?php

namespace App\Console\Commands;

use App\Stats\DailyStatsRecorder;
use Illuminate\Console\Command;

class RecordStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peopledb:record-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record the stats for today';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new DailyStatsRecorder())->record();

        return Command::SUCCESS;
    }
}
