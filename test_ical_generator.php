<?php

require_once __DIR__ . '/bootstrap.php';

use App\Calendar\iCalGenerator;

$icalGenerator = $container->getICalGenerator();

$eventData = [
    'summary' => 'My Awesome Lesson',
    'description' => 'This is a test lesson for iCal generation.',
    'location' => 'Online via Zoom',
    'dtstart' => date('Ymd\THis', strtotime('+1 day')),
    'dtend' => date('Ymd\THis', strtotime('+1 day +1 hour')),
];

try {
    $icalOutput = $icalGenerator->generateEvent($eventData);
    echo "Generated iCal Event:\n";
    echo $icalOutput;
} catch (Exception $e) {
    echo "Error generating iCal: " . $e->getMessage() . "\n";
}

