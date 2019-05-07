<?php

namespace App\Jobs;

use App\LeaveDay;
use App\Services\GoogleCalendar;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteLeaveDay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $leaveDay;

    /**
     * Create a new job instance.
     *
     * @param LeaveDay|null $leaveDay
     */
    public function __construct(LeaveDay $leaveDay = null)
    {
        $this->leaveDay = $leaveDay;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new GoogleCalendar())->deleteEvent($this->leaveDay);
    }
}
