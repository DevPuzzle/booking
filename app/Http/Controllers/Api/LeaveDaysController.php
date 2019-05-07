<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\LeaveDay;
use App\Traits\Recurring;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaveDaysController extends Controller
{
    use Recurring;
    public function index(Request $request)
    {
        $user = auth()->user();
        $per_page = $request->get('perPage', 20);
        $start_date = Carbon::createFromFormat('m/d/Y',$request->get('startDate'));
        $end_date = Carbon::createFromFormat('m/d/Y',$request->get('endDate'));
        if ($user->role == 'administrator' || $user->role == 'mngr') {
            $leave_days = LeaveDay::with('user')->get();
            $leave_days = $this->appendRecurring($leave_days);
            $leave_days = $this->filterBetween($leave_days, $start_date, $end_date);
            $leave_days = $this->sortByEndsAt($leave_days);
            $leave_days = new LengthAwarePaginator(
                $this->groupByDate($leave_days->forPage($request->get('page', 1), $per_page)),
                $leave_days->count(),
                $per_page);
            return $leave_days;
        } else{
            $leave_days = LeaveDay::with('user')->where('user_id', '=', $user->id)->get();
            $leave_days = $this->appendRecurring($leave_days);
            $leave_days = $this->filterBetween($leave_days, $start_date, $end_date);
            $leave_days = $this->sortByEndsAt($leave_days);
            $leave_days = new LengthAwarePaginator(
                $this->groupByDate($leave_days->forPage($request->get('page', 1), $per_page)),
                $leave_days->count(),
                $per_page);
            $data['leave_days'] = $leave_days;
            $data['user_name'] = $user->name;
            return $data;
        }
    }
    public function user(Request $request)
    {
        $username = explode('/',$request->url())[6];
        $user = User::where('username', '=', $username)->get()->first();
        $per_page = $request->get('perPage', 10);
        $start_date = Carbon::createFromFormat('m/d/Y',$request->get('startDate'));
        $end_date = Carbon::createFromFormat('m/d/Y',$request->get('endDate'));
        $leave_days = LeaveDay::where('user_id', '=', $user->id)->get();
        $leave_days = $this->appendRecurring($leave_days);
        $leave_days = $this->filterBetween($leave_days, $start_date, $end_date);
        $leave_days = $this->sortByEndsAt($leave_days);
        $leave_days = new LengthAwarePaginator(
            $this->groupByDate($leave_days->forPage($request->get('page', 1), $per_page)),
            $leave_days->count(),
            $per_page);
        $data['leave_days'] = $leave_days;
        $data['user_name'] = $user->name;
        return $data;
    }
}
