<?php

namespace App\Http\Controllers;

use App\LeaveDay;
use App\Setting;
use Illuminate\Http\Request;

class LeaveDaysController extends Controller
{
    public function index()
    {
        return \Redirect::to('leavedays');
        $hasCalendars = !!Setting::getValue(Setting::READ_CALENDAR_ID_KEY);
        return view('leave-days.index', compact('hasCalendars'));
    }

    public function update(Request $request, LeaveDay $leaveDay)
    {
        $leaveDay->update($request->only('summary', 'description', 'starts_at', 'ends_at', 'recurrence'));
        return response()->json(['status' => true]);
    }

    public function store(Request $request)
    {
        $leaveDay = new LeaveDay($request->only('summary', 'description', 'starts_at', 'ends_at', 'recurrence'));
        $user = auth()->user();
        $user->leaveDays()->save($leaveDay);
        return response()->json(['status' => true]);
    }

    public function destroy(LeaveDay $leaveDay)
    {
        $leaveDay->delete();
        return response(null, 204);
    }

    public function user()
    {
        return view('leave-days.user');
    }
}
