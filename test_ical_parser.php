<?php

require_once __DIR__ . '/bootstrap.php';

use App\Calendar\iCalParser;

$icalParser = $container->getICalParser();

// Example iCal data (you would typically read this from a file or URL)
$icalString = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//hacksw/handcal//NONSGML v1.0//EN\r\nBEGIN:VEVENT\r\nUID:19970901T130000Z-123401@example.com\r\nDTSTAMP:19970901T130000Z\r\nDTSTART:19970901T130000Z\r\nDTEND:19970901T170000Z\r\nSUMMARY:Bastille Day Party\r\nEND:VEVENT\r\nEND:VCALENDAR";

try {
    $events = $icalParser->parseString($icalString);
    echo "Parsed iCal Events:\n";
    print_r($events);
} catch (Exception $e) {
    echo "Error parsing iCal: " . $e->getMessage() . "\n";
}

// You can also test with a file:
// file_put_contents('test.ics', $icalString);
// try {
//     $eventsFromFile = $icalParser->parseFile('test.ics');
//     echo "\nParsed iCal Events from file:\n";
//     print_r($eventsFromFile);
// } catch (Exception $e) {
//     echo "Error parsing iCal from file: " . $e->getMessage() . "\n";
// }

