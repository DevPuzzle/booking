<?php

namespace App\Traits;

use App\LeaveDay;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait Recurring
{
    public function appendRecurring(Collection $data) : Collection
    {
        $without_recurring = $data->filter(function ($item) {
            return !$item->recurrence;
        })->toArray();
        $with_recurring = $data->filter(function ($item) {
            return $item->recurrence;
        })->map(function (LeaveDay $leaveDay) {
            return $leaveDay->recurringDates()
                ->map(function (Carbon $start) use ($leaveDay) {
                    $duration = $leaveDay->starts_at->diff($leaveDay->ends_at);
                    $end = (clone $start)->add($duration);
                    $leaveDay->fill([
                        'starts_at' => $start,
                        'ends_at' => $end
                    ]);
                    $leaveDay->append('human_readable_recurrence');
                    return $leaveDay->toArray();
                });

        })->collapse()->toArray();
        return collect(array_merge($without_recurring, $with_recurring));
    }

    public function groupByDate(Collection $data) : Collection
    {
        return $data->groupBy(function ($day) {
            return Carbon::parse($day['starts_at'])->format('d-m-Y');
        });
    }

    public function filterUpcoming(Collection $data) : Collection
    {
        return $data->filter(function ($day) {
            return Carbon::parse($day['ends_at'])->isFuture();
        });
    }

    public function filterBetween(Collection $data ,$start_date, $end_date) : Collection
    {
        return $data->filter(function ($day) use ($start_date, $end_date){
            return Carbon::parse($day['starts_at'])->gt($start_date) && Carbon::parse($day['ends_at'])->lt($end_date);
        });
    }

    public function sortByEndsAt(Collection $data) : Collection
    {
        return collect($data->sortBy(function ($day) {
            return Carbon::parse($day['ends_at']);
        })->values()->all());
    }
}