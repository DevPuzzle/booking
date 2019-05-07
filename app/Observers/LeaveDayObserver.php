<?php

namespace App\Observers;

use App\Jobs\DeleteLeaveDay;
use App\Jobs\WriteLeaveDay;
use App\LeaveDay;
use App\Setting;

class LeaveDayObserver
{
    public function saved(LeaveDay $leaveDay)
    {
        return;
        if (Setting::getValue(Setting::WRITE_CALENDAR_ID_KEY)) {
            if (!$leaveDay->write_id || is_null($leaveDay->write_at) || $leaveDay->write_at->lt($leaveDay->updated_at)) {
                dispatch(new WriteCalendar($leaveDay));
            }
        }

    }

    public function deleted(LeaveDay $leaveDay)
    {
        if (Setting::getValue(Setting::WRITE_CALENDAR_ID_KEY)) {
            if ($leaveDay->write_id) {
                dispatch(new DeleteLeaveDay($leaveDay));
            }
        }
    }
}
