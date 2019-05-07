<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RRule\RRule;

class LeaveDay extends Model
{
    protected $fillable = ['summary', 'description', 'starts_at', 'ends_at', 'recurrence'];
    protected $dates = ['starts_at', 'ends_at', 'write_at', 'read_at'];
    protected $appends = ['is_all_day'];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', Carbon::now());
    }

    public function recurringDates()
    {
        try {
            $recurrence = $this->getRecurrence();
            $dstart = $this->starts_at->format('Ymd\THis\Z');

            if (str_contains($recurrence, 'UNTIL')) {
                $until = str_replace('UNTIL=', '', head(preg_grep('/UNTIL=(\w*);{0,1}/', explode(';', $recurrence))));
                $dstart = strlen($until) === 8 ? $this->starts_at->format('Y-m-d') : $this->starts_at;
            }

            $instances = new RRule($recurrence, $dstart);
            return collect($instances->getOccurrences(100))->map(function ($date) {
                return Carbon::instance($date);
            });
        } catch (\Exception $e) {
            throw  $e;
//            dd($e->getMessage(), $until, $this->starts_at->format($format), Carbon::parse($until)->diffForHumans($this->starts_at), strlen($this->starts_at->format($format)) , $this->getRecurrence());
//            \Log::error($e->getMessage(), $e->getTrace());
            return collect();
        }
    }

    public function getRecurrenceAttribute($value)
    {
        if ($value) {
            return unserialize($value);
        }
        return $value;
    }

    public function setRecurrenceAttribute($value)
    {
        $this->attributes['recurrence'] = serialize($value);
    }

    private function getRecurrence()
    {
        if (!is_array($this->recurrence)) {
            return $this->recurrence;
        }
        $rules = collect($this->recurrence);

        $filtered = $rules->filter(function ($rr) {
            return explode(':', $rr)[0] == 'RRULE';
        });

        return $filtered->count() ? $filtered->first() : $rules->first();
    }

    public function getHumanReadableRecurrenceAttribute()
    {
        $recurrence = $this->getRecurrence();
        try {
            $dstart = $this->starts_at->format('Ymd\THis\Z');

            if (str_contains($recurrence, 'UNTIL')) {
                $until = str_replace('UNTIL=', '', head(preg_grep('/UNTIL=(\w*);{0,1}/', explode(';', $recurrence))));
                $dstart = strlen($until) === 8 ? $this->starts_at->format('Y-m-d') : $this->starts_at;
            }

            return (new RRule($recurrence, $dstart))->humanReadable([
                'date_formatter' => function ($date) {
                    return $date->format('jS F');
                }
            ]);
        } catch (\Throwable $e) {
            dd($e->getMessage(), $recurrence);
        }
    }

    public function getIsAllDayAttribute()
    {
        if ($this->starts_at) {
            return $this->starts_at->eq($this->ends_at);
        }
        return false;
    }

}
