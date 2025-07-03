<?php

namespace App\Calendar;

use Sabre\VObject;

class iCalParser
{
    public function parseFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("iCal file not found: " . $filePath);
        }
        $vcalendar = VObject\Reader::read(file_get_contents($filePath));
        return $this->extractEvents($vcalendar);
    }

    public function parseString(string $iCalString): array
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $iCalString);
        rewind($stream);
        $vcalendar = VObject\Reader::read($stream);
        fclose($stream);
        return $this->extractEvents($vcalendar);
    }

    private function extractEvents(VObject\Component\VCalendar $vcalendar): array
    {
        $events = [];
        foreach ($vcalendar->VEVENT as $event) {
            $events[] = [
                'uid' => (string) $event->UID,
                'summary' => (string) $event->SUMMARY,
                'description' => (string) $event->DESCRIPTION,
                'location' => (string) $event->LOCATION,
                'dtstart' => (string) $event->DTSTART,
                'dtend' => (string) $event->DTEND,
                'rrule' => (string) $event->RRULE,
                'exdate' => (string) $event->EXDATE,
                'status' => (string) $event->STATUS,
            ];
        }
        return $events;
    }
}
