<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; font-family: Arial; }
h1, p { margin: 5px; }
code { background: #fff8d6; display: block; margin: 8px 5px; }
table { border-spacing: 0; margin: 5px; border: #ffc571 1px solid; }
td { border: #e1e1e1 1px solid; padding: 1px 2px; background: #f7ffe4; }
h2 { margin: 8px 0 0 5px; }
.month { color: #ff7b00; }
.week { color: rgba(0, 47, 255, 0.56); }
.daynames { color: #6dba54; }
.outday { color: #989996; }
.extra { font-weight: bold; color: #aa0086; }
.italic { font-style: italic; }
.funny, .funny td { background: yellow; border: 1px green dotted; }
.myMonth { border: 2px #000 solid; }
</style>
</head>
<body>

<h1>PHP Calendar usage and examples</h1>
<p>Download here <a href="https://github.com/VladaHejda/calendarPHP#php-calendar">github.com/VladaHejda/calendarPHP</a></p>

<?php

require __DIR__ . '/../src/Calendar.php';

function show($code) {
	echo "\n\n" . highlight_string("<?php\n" . $code, TRUE) . "\n\n";
}


echo '<h2>Common use</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar->render(4, 2010);
EOD;
show($code);
eval($code);


echo '<h2>Change day pattern (Roman numerals - %e)</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setDayPattern('%e.')
	->render(5, 2010);
EOD;
show($code);
eval($code);


echo '<h2>Change outside days pattern</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setOutsideDayPattern('&nbsp;')
	->render(6, 2010);
EOD;
show($code);
eval($code);


echo '<h2>Change week starting day (0 = Sunday)</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setStartDay(1)
	->render(7, 2010);
EOD;
show($code);
eval($code);


echo '<h2>Change week pattern (Roman numerals)</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setWeekPattern('%e.')
	->render(8, 2010);
EOD;
show($code);
eval($code);


echo '<h2>Pad day numbers with leading zero</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setZerofillDays()
	->render(9, 2010);
EOD;
show($code);
eval($code);


echo '<h2>Add extra class for certain dates</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setExtraDateClass(new \DateTime('2010-10-15'), 'extra')
	->setExtraDateClass(new \DateTime('2010-10-25'), 'extra')
	->render(10, 2010);
EOD;
show($code);
eval($code);


echo '<h2>Add extra class for certain period</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
$period = new \DatePeriod(new \DateTime('2010-11-11'), new DateInterval('P1D'), 8);
echo $calendar->setExtraPeriodClass($period, 'extra')
	->render(11, 2010);
EOD;
show($code);
eval($code);


echo '<h2>Use extra pattern for certain date/period</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
$period = new \DatePeriod(new \DateTime('2010-12-20'), new DateInterval('P2D'), 2);
echo $calendar
	->setExtraDatePattern(new \DateTime('2010-12-13'), '<b>XXX</b>')
	->setExtraPeriodPattern($period, '<b>XXX</b>')
	->render(12, 2010);
EOD;
show($code);
eval($code);


echo '<h2>Week of last year in new one</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar->render(1, 2011);
EOD;
show($code);
eval($code);



echo '<h2>How extra classes overlaying</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
$period1 = new \DatePeriod(new \DateTime('2011-02-10'), new DateInterval('P1D'), 4);
$period2 = new \DatePeriod(new \DateTime('2011-02-12'), new DateInterval('P1D'), 4);
echo $calendar
	->setExtraPeriodClass($period1, 'extra')
	->setExtraPeriodClass($period2, 'italic')
	->render(2, 2011);
EOD;
show($code);
eval($code);


echo '<h2>Pad week numbers with leading zero</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setZerofillWeeks()
	->render(3, 2011);
EOD;
show($code);
eval($code);


echo '<h2>Change day / month default headings</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setDayHeadings(['ne', 'po', 'ut', 'st', 'ct', 'pa', 'so'])
	->setMonthHeadings(['leden','unor','brezen','duben','kveten','cerven','cervenec','srpen','zari','rijen','listopad','prosinec'])
	->render(4, 2011);
EOD;
show($code);
eval($code);


echo '<h2>Exclude headings</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setIncludeDayHeadings(FALSE)
	->setIncludeMonthHeadings(FALSE)
	->render(5, 2011);
EOD;
show($code);
eval($code);


echo '<h2>Exclude week numbers</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setIncludeDayHeadings(FALSE)
	->setIncludeMonthHeadings(FALSE)
	->setIncludeWeekNumbers(FALSE)
	->render(6, 2011);
EOD;
show($code);
eval($code);


echo '<h2>Change classnames of table structure</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setTableClass('funny')
	->setMonthNameRowClass('funny')
	->setWeekNumberCellClass('funny')
	->setDayNamesRowClass('funny')
	->setOutsideDayCellClass('funny')
	->render(7, 2011);
EOD;
show($code);
eval($code);


echo '<h2>Add table classname for certain month + add classname for certain day of week</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setMonthClasses([7 => 'myMonth'])
	->setDayOfWeekClasses([0 => 'funny', 5 => 'funny'])
	->render(8, 2011);
EOD;
show($code);
eval($code);


echo '<h2>Use extra pattern / class outside month scope</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setExtraDateClass(new \DateTime('2011-08-29'), 'funny')
	->setExtraDateClass(new \DateTime('2011-09-10'), 'funny')
	->setExtraDatePattern(new \DateTime('2011-10-01'), '<b>XXX</b>')
	->setExtraDatePattern(new \DateTime('2011-09-15'), '<b>XXX</b>')
	->setApplyExtraPatternsToOutsideDays()
	->render(9, 2011);
EOD;
show($code);
eval($code);


echo '<h2>Forbid using extra pattern / class outside month scope</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setExtraDateClass(new \DateTime('2011-08-29'), 'funny')
	->setExtraDateClass(new \DateTime('2011-09-10'), 'funny')
	->setExtraDatePattern(new \DateTime('2011-10-01'), '<b>XXX</b>')
	->setExtraDatePattern(new \DateTime('2011-09-15'), '<b>XXX</b>')
	->setAddExtraClassesToOutsideDays(FALSE)
	->render(9, 2011);
EOD;
show($code);
eval($code);


$calendar = new \Calendar;
echo '<h2>Add week number heading</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setWeekNumbersHeading('week')
	->render(10, 2011);
EOD;
show($code);
eval($code);



$calendar = new \Calendar;
echo '<h2>Do not render HTML indention (see source code)</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->render(11, 2011, FALSE);
EOD;
show($code);
eval($code);



$calendar = new \Calendar;
echo '<h2>Add HTML indention offset, change indention string (see source code)</h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->render(12, 2011, 5, ' ');
EOD;
show($code);
eval($code);

?>
</body>
</html>
