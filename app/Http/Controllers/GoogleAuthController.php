<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use App\Setting;
use \Google_Service_People;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
       
        $scopes = [
        \Google_Service_Calendar::CALENDAR,
            'openid', 'profile', 'email', Google_Service_People::CONTACTS_READONLY,
        ];
        $parameters = ['access_type' => 'offline', "prompt" => "consent select_account"];
        
        return Socialite::driver('google')->scopes($scopes)->with($parameters)->redirect();
    }

    public function callback()
    {
        //dd(request()->all());
        $user = Socialite::with('google')->user();
        //dd($user);
        Setting::setValues([
            'google_calendar_token' => $user->token,
            'google_calendar_refresh_token' => $user->refreshToken,
            'google_calendar_user_email' => $user->email,
            'google_cal_token_expires_at' => Carbon::now()->addHour()->toDateTimeString()
        ]);
        return redirect()->route('settings.calendar.update', ['user' => $user]);
    }
}
