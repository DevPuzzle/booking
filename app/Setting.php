<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $fillable = ['key', 'value'];

    const WRITE_CALENDAR_ID_KEY = 'write_calendar_id';
    const WRITE_CALENDAR_SUMMARY_KEY = 'write_calendar_name';
    const READ_CALENDAR_ID_KEY = 'read_calendar_id';
    const READ_CALENDAR_SUMMARY_KEY = 'read_calendar_name';

    public static function getValue($key)
    {
        return static::whereKey($key)->value('value');
    }

    public static function setValue($key, $value)
    {
        return static::updateOrCreate(['key' => $key],['value' => $value]);
    }

    public static function setValues(array $values)
    {
        return array_map(function($key, $value){
            return static::updateOrCreate(['key' => $key],['value' => $value]);
        }, array_keys($values), $values);
    }

    public static function getValues(array $keys)
    {
        return array_map(function($key){
            return static::whereKey($key)->value('value');
        }, $keys);
    }

    public static function getCalendar($calendar_name)
    {
        return [
            'id' => static::whereKey($calendar_name.'_id')->value('value'),
            'name' => static::whereKey($calendar_name.'_name')->value('value'),
            'summary' => static::whereKey($calendar_name.'_summary')->value('value'),
        ];
    }

    public static function getCalendars()
    {
        return collect([
            'read_calendar' => [
                'id' => static::whereKey(self::READ_CALENDAR_ID_KEY)->value('value'),
                'summary' => static::whereKey(self::READ_CALENDAR_SUMMARY_KEY)->value('value'),
            ],
            'write_calendar' => [
                'id' => static::whereKey(self::WRITE_CALENDAR_ID_KEY)->value('value'),
                'summary' => static::whereKey(self::WRITE_CALENDAR_SUMMARY_KEY)->value('value'),
            ],
        ]);
    }
}
