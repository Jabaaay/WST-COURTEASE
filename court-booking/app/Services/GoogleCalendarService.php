<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendarService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setScopes(Google_Service_Calendar::CALENDAR);
        $this->client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        $this->service = new Google_Service_Calendar($this->client);
    }

    public function addEvent($booking)
    {
        $event = new Google_Service_Calendar_Event([
            'summary' => $booking->event_name,
            'description' => "Equipment Request: " . $booking->equipment_request,
            'start' => [
                'dateTime' => $booking->booking_date,
                'timeZone' => 'Asia/Manila',
            ],
            'end' => [
                'dateTime' => $booking->end_time,
                'timeZone' => 'Asia/Manila',
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60],
                    ['method' => 'popup', 'minutes' => 30],
                ],
            ],
        ]);

        try {
            $calendarId = 'primary';
            $event = $this->service->events->insert($calendarId, $event);
            return $event->getId();
        } catch (\Exception $e) {
            \Log::error('Failed to add event to Google Calendar: ' . $e->getMessage());
            return null;
        }
    }

    public function updateEvent($booking)
    {
        try {
            $event = $this->service->events->get('primary', $booking->google_calendar_event_id);
            
            $event->setSummary($booking->event_name);
            $event->setDescription("Equipment Request: " . $booking->equipment_request);
            $event->setStart([
                'dateTime' => $booking->booking_date,
                'timeZone' => 'Asia/Manila',
            ]);
            $event->setEnd([
                'dateTime' => $booking->end_time,
                'timeZone' => 'Asia/Manila',
            ]);

            $updatedEvent = $this->service->events->update('primary', $event->getId(), $event);
            return $updatedEvent->getId();
        } catch (\Exception $e) {
            \Log::error('Failed to update event in Google Calendar: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteEvent($eventId)
    {
        try {
            $this->service->events->delete('primary', $eventId);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to delete event from Google Calendar: ' . $e->getMessage());
            return false;
        }
    }
} 