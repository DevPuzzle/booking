<?php

namespace App\Console\Commands;

use App\Jobs\WriteLeaveDays;
use Illuminate\Console\Command;
use App\Jobs\ReadCalendar;

class SyncCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports Data from the set read Google Calendar.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * Dispatch a job to sync the calendar.
         */
        $this->info('Read Started.');
        ReadCalendar::dispatch();
        $this->info('Read Complete.');
        $this->info('Write Started.');
        WriteLeaveDays::dispatch();
        $this->info('Write Complete.');

    }
}
