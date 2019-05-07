<?php

namespace App\Jobs;

use App\Services\GoogleCalendar;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ReadCalendar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // TODO: Throw exception when the calendar token is expired.
        $events = (new GoogleCalendar())->listEventsFromReadCalendar();
        $event_items = collect($events->getItems());
        
        //dd($event_items);
        
        while (true) {
            $data =
            /*$this->insertDb(*/
                $event_items->filter(function ($event) {
                    return isset($event->summary);
                })->filter(function ($event){
                    $split_summary = preg_split('/^([\w\d]*)/', $event->summary,
                        null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                    $username = $split_summary[0];
                    $user = User::where('username', $username)->first();
                    return isset($user);
                })->map(function ($event) {
                    $split_summary = preg_split('/^([\w\d]*)/', $event->summary,
                        null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                    $username = $split_summary[0];
                    $summary = isset($split_summary[1]) ? ltrim($split_summary[1]) : '';
                    $user = User::where('username', $username)->first();
                    $recurrence = $event->recurrence;
                    if ($recurrence) {
                        $options = $event->recurrence;
                        $rrule = collect($options)->filter(function ($option) {
                            return explode(':', $option)[0] == 'RRULE';
                        })->first();
                        $rrules = explode(';', explode(':', $rrule)[1]);
                        $until_rule = collect($rrules)->filter(function ($rule) {
                            return explode('=', $rule)[0] == 'UNTIL';
                        })->first();
                        if ($until_rule) {
                            $date_time_string = explode('=', $until_rule)[1];
                            if(strlen($date_time_string) <= 8 ){
                                $last_instance_end_date = Carbon::parse($date_time_string)
                                    ->addHours(23)
                                    ->addMinutes(59)
                                    ->addSeconds(59)
                                    ->toDateTimeString();
                            }else{
                                $last_instance_end_date = Carbon::parse($date_time_string)->toDateTimeString();
                            }
                        }else{
                            $last_instance_end_date = Carbon::now()->addYears(10)->toDateString();
                        }
                    }else{
                        $last_instance_end_date = null;
                    }
                    return [
                        'read_id' => $event->id,
                        'html_link' => $event->htmlLink,
                        'summary' => $summary,
                        'recurrence' => isset($recurrence[0]) ? $recurrence[0] : $recurrence,
                        /*'rrules' => isset($rrules) ? $rrule : null,
                        '$until_rule' => isset($until_rule) ? $until_rule : null,*/
                        'recurring_last_instance_date' => $last_instance_end_date,
                        'description' => $event->description,
                        'starts_at' => Carbon::parse($event->start->dateTime)->toDateTimeString(),
                        'ends_at' => Carbon::parse($event->end->dateTime)->toDateTimeString(),
                        'user_id' => $user ? $user->id : null,
                        'added_by' => 0
                    ];
                })->all();
            /*);*/
            
            $this->insertDb($data);
            //dd($data); 
            
               $pageToken = $events->getNextPageToken();
            if ($pageToken) {
                $events = (new GoogleCalendar())->listEventsFromReadCalendar($pageToken);
                $event_items = collect($events->getItems());;
            } else {
                $sync_token = $events->getNextSyncToken();
                Setting::setValue('read_calendar_last_sync_token', $sync_token);
                break;
            }
            echo '.';
        }
        Setting::setValue('read_calendar_last_sync', Carbon::now()->toDateTimeString());
        dispatch( new WriteLeaveDays());
    }

    private function insertDb($data)
    {
        DB::table('leave_days')->insert($data);
    }
}