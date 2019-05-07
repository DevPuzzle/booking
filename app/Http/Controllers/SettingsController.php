<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendar;
use App\Setting;
use Illuminate\Http\Request;
use App\Jobs\ReadCalendar;

class SettingsController extends Controller
{
    public function index()
    {
        $data['calendars']['read_calendar'] = Setting::getCalendar('read_calendar');
        $data['calendars']['write_calendar'] = Setting::getCalendar('write_calendar');
        $data['user'] = auth()->user();
        return view('settings.index', $data);
    }

    public function updateUser(Request $request)
    {
        $user = auth()->user();
        $user->update($request->only('username', 'name', 'phone_no'));
        return response()->json(array('status' => true));
    }

    public function updateCalendarSettings()
    {
        $googleCalendar = new GoogleCalendar();
        $data['update_calendars'] = $googleCalendar->getCalendars();
        $data['gcalEmail'] = Setting::getValue('google_calendar_user_email');
        $data['user'] = auth()->user();
        return view('settings.index', $data);
    }

    public function updateCalendar(Request $request)
    {
        $settings = [
            Setting::READ_CALENDAR_ID_KEY => $request->get('read_calendar')['id'],
            Setting::READ_CALENDAR_SUMMARY_KEY => $request->get('read_calendar')['summary'],
            Setting::WRITE_CALENDAR_ID_KEY => $request->get('write_calendar')['id'],
            Setting::WRITE_CALENDAR_SUMMARY_KEY => $request->get('write_calendar')['summary'],
        ];
        Setting::setValues($settings);
        return response()->json(array('status' => true));
    }

    public function syncCalendar()
    {
        
        ReadCalendar::dispatch();
        return redirect()->route('leave-days.index');
    }

}
