<?php

namespace App\Jobs;

use App\LeaveDay;
use App\Services\GoogleCalendar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use \Google_Service_Exception;

class WriteLeaveDays implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $calendar = new GoogleCalendar();
        //get events without w_id
        $query = LeaveDay::whereNull('write_id');
        
        //dd($query);
        //in chunks of x push to google
        $query->chunk(20, function ($collection) use ($calendar) {
            $events = $calendar->createEvents($collection);
            //on success update write id and write at
            
            collect($events)
                ->each(function ($event, $id) {
                    
                    if($event instanceof Google_Service_Exception ){
                        return;
                    }
                    
                    $id = str_replace('response-', '', $id);
                    DB::table('leave_days')->where('id', $id)
                        ->update([
                            'write_id' => $event->getId(),
                            'write_at' => date('Y-m-d H:i:s')
                        ]);
                });
        });
    }
}
