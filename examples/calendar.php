<?php

require __DIR__ . '/../src/Calendar.php';

$calendar = new \Calendar;

echo $calendar->render(5, 2010);
