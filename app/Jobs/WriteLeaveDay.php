<?php

namespace App\Jobs;

use App\LeaveDay;
use App\Services\GoogleCalendar;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WriteLeaveDay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $leaveDay;

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
        try {
            $calendar = new GoogleCalendar();
            $this->syncEvent($this->leaveDay, $calendar);
            Setting::setValue('write_calendar_last_sync', Carbon::now()->toDateTimeString());
            echo 'Synced Leave Day: ' . $this->leaveDay->id;
        } catch (\Google_Service_Exception $e) {
            Log::info('Writing Event', $this->leaveDay->toArray());
            Log::error($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * @param LeaveDay $leaveDay
     * @param GoogleCalendar $calendar
     */
    public function syncEvent(LeaveDay $leaveDay, GoogleCalendar $calendar): void
    {
        if ($leaveDay->write_id) {
            $calendar->updateEvent($leaveDay);
        } else {
            $event = $calendar->createEvent($leaveDay);
            $leaveDay->write_id = $event->getId();
        }
        $leaveDay->write_at = Carbon::now();
        $leaveDay->save();
    }
}
