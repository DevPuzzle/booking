<?php

namespace App\Console;

use App\Jobs\ReadCalendar;
use App\Jobs\WriteLeaveDays;
use App\Setting;
use Google_Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */

    protected function schedule(Schedule $schedule)
    {
        //create a cron for refreshing the access token- run every 3 min and check if token expires in next 15 min
        $schedule->call(function () {
            $client = new Google_Client();
            $client->setAccessToken(Setting::getValue('google_calendar_token'));
            $client->refreshToken(Setting::getValue('google_calendar_refresh_token'));
            $refreshToken = $client->getRefreshToken();
            $accessToken = $client->getAccessToken()['access_token'];
            Setting::setValues([
                'google_calendar_token' => $accessToken,
                'google_calendar_refresh_token' => $refreshToken,
                'google_cal_token_expires_at' => Carbon::now()->addHour()->toDateTimeString()
            ]);
        })
            ->cron('*/3 * * * *')
            ->when(function () {
                $expiryTime = Setting::getValue('google_cal_token_expires_at');
                $hasToken = !!Setting::getValue('google_calendar_token');
                return $hasToken && Carbon::parse($expiryTime)->diffInMinutes() < 15;
            });
        $schedule->call(function(){
            dispatch(new ReadCalendar());
            dispatch(new WriteLeaveDays());
        })->everyFiveMinutes();
//        $schedule->command('inspire')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
