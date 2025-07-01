<?php

namespace App\Calendar;

use Sabre\VObject;

class iCalGenerator
{
    public function generateEvent(array $eventData): string
    {
        $vcalendar = new VObject\Component\VCalendar();
        $vcalendar->add('VEVENT', [
            'UID' => $eventData['uid'] ?? uniqid(),
            'SUMMARY' => $eventData['summary'] ?? 'New Event',
            'DTSTART' => $eventData['dtstart'] ?? date('Ymd\THis'),
            'DTEND' => $eventData['dtend'] ?? date('Ymd\THis', strtotime('+1 hour')),
            'DESCRIPTION' => $eventData['description'] ?? '',
            'LOCATION' => $eventData['location'] ?? '',
        ]);

        return $vcalendar->serialize();
    }
}
