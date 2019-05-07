<?php

namespace App\Services;

use App\LeaveDay;
use App\Setting;
use Google_Client;
use Google_Http_Batch;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Support\Collection;

class GoogleCalendar
{

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAccessToken(Setting::getValue('google_calendar_token'));
        $this->service = new Google_Service_Calendar($this->client);
    }

    public function getCalendars()
    {
        return collect($this->service->calendarList->listCalendarList()->getItems())->map(function ($calendar) {
            return $this->getCalendar($calendar->id);
        });
    }

    public function getCalendar($calendarId)
    {
        return $this->service->calendars->get($calendarId);
    }

    public function listEventsFromReadCalendar($pageToken = null)
    {
        $calendarId = Setting::getValue(Setting::READ_CALENDAR_ID_KEY);
        $optParams = array('maxResults' => 20);
        if(!!Setting::getValue('read_calendar_last_sync_token')){
            $optParams['syncToken'] = Setting::getValue('read_calendar_last_sync_token');
        }
        if ($pageToken) {
            $optParams['pageToken'] = $pageToken;
            $events = $this->service->events->listEvents($calendarId, $optParams);
        } else {
            $events = $this->service->events->listEvents($calendarId, $optParams);
        }
        return $events;
    }

    public function createEvent(LeaveDay $leaveDay)
    {
        $write_calendar_id = Setting::getValue(Setting::WRITE_CALENDAR_ID_KEY);
        $summary = $this->eventSummary($leaveDay);
        $event = new Google_Service_Calendar_Event([
            'summary' => $summary,
            'description' => $leaveDay->description,
            'start' => [
                'dateTime' => $leaveDay->starts_at ? $leaveDay->starts_at->toRfc3339String() : null,
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => $leaveDay->ends_at ? $leaveDay->ends_at->toRfc3339String() : null,
                'timeZone' => config('app.timezone'),
            ],
            'recurrence' => $leaveDay->recurrence,
            'reminders' => [
                'useDefault' => TRUE,
            ]
        ]);
        return $this->service->events->insert($write_calendar_id, $event);
    }

    public function updateEvent(LeaveDay $leaveDay)
    {
        $write_calendar_id = Setting::getValue(Setting::WRITE_CALENDAR_ID_KEY);
        $summary = $this->eventSummary($leaveDay);
        $event = $this->service->events->get($write_calendar_id, $leaveDay->write_id);
        $event->setSummary($summary);
        $event->setDescription($leaveDay->description);
        $event->setStart($leaveDay->starts_at);
        $event->setEnd($leaveDay->ends_at);
        $event->setRecurrence($leaveDay->recurrence);
        $updatedEvent = $this->service->events->update($write_calendar_id, $event->getId(), $event);
        return $updatedEvent->getUpdated();
    }

    public function deleteEvent(LeaveDay $leaveDay)
    {
        $write_calendar_id = Setting::getValue(Setting::WRITE_CALENDAR_ID_KEY);
        $this->service->events->delete($write_calendar_id, $leaveDay->write_id);
    }

    /**
     * @param LeaveDay $leaveDay
     * @return string
     */
    private function eventSummary(LeaveDay $leaveDay): string
    {
        $summary = $leaveDay->summary && trim($leaveDay->summary) !== '' ?
            $leaveDay->user->username . ' - ' . $leaveDay->summary : $leaveDay->user->username;
        return $summary;
    }

    private function batchRequests(Collection $requests){
        $batch = new Google_Http_Batch($this->client);
        $requests->map(function ($req, $id) use ($batch){
            $batch->add($req, $id);
        });
        return $batch->execute();
    }

    public function createEvents(Collection $leaveDays){
        $this->client->setUseBatch(true);
        $requests = $leaveDays->keyBy('id')->map(function ($leaveDay){
            return $this->createEvent($leaveDay);
        });
        return $this->batchRequests($requests);
    }
}