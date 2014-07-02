<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; font-family: Arial; }
table { border-spacing: 0; margin: 5px; border: #ffc571 1px solid; }
td { border: #e1e1e1 1px solid; padding: 1px 2px; background: #f7ffe4; }
h2 { margin-top: 8px; }
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

<?php

require __DIR__ . '/../src/Calendar.php';


$calendar = new \Calendar;
echo '<h2>Standard</h2>';
echo $calendar->render(4, 2010);


$calendar = new \Calendar;
echo '<h2>Roman numerals</h2>';
echo $calendar->setDayPattern('%e.')
	->render(5, 2010);


$calendar = new \Calendar;
echo '<h2>No outside days</h2>';
echo $calendar->setOutsideDayPattern('&nbsp;')
	->render(6, 2010);


$calendar = new \Calendar;
echo '<h2>Another week starting day</h2>';
echo $calendar->setStartDay(1)
	->render(7, 2010);


$calendar = new \Calendar;
echo '<h2>Week Roman numerals</h2>';
echo $calendar->setWeekPattern('%e.')
	->render(8, 2010);


$calendar = new \Calendar;
echo '<h2>Zero padded day numbers</h2>';
echo $calendar->setZerofillDays()
	->render(9, 2010);


$calendar = new \Calendar;
echo '<h2>Certain dates class</h2>';
echo $calendar->setExtraDateClass(new DateTime('2010-10-15'), 'extra')
	->setExtraDateClass(new DateTime('2010-10-25'), 'extra')
	->render(10, 2010);


$calendar = new \Calendar;
echo '<h2>Certain period class</h2>';
$period = new DatePeriod(new DateTime('2010-11-11'), new DateInterval('P1D'), 8);
echo $calendar->setExtraPeriodClass($period, 'extra')
	->render(11, 2010);


$calendar = new \Calendar;
echo '<h2>Certain date/period pattern</h2>';
$period = new DatePeriod(new DateTime('2010-12-20'), new DateInterval('P2D'), 2);
echo $calendar->setExtraDatePattern(new DateTime('2010-12-13'), '<b>X</b>')
	->setExtraPeriodPattern($period, '<b>X</b>')
	->render(12, 2010);


$calendar = new \Calendar;
echo '<h2>Week of last year in new one</h2>';
echo $calendar->render(1, 2011);


$calendar = new \Calendar;
echo '<h2>Extra classes overlay</h2>';
$period1 = new DatePeriod(new DateTime('2011-02-10'), new DateInterval('P1D'), 4);
$period2 = new DatePeriod(new DateTime('2011-02-12'), new DateInterval('P1D'), 4);
echo $calendar->setExtraPeriodClass($period1, 'extra')
	->setExtraPeriodClass($period2, 'italic')
	->render(2, 2011);


$calendar = new \Calendar;
echo '<h2>Zero padded week numbers</h2>';
echo $calendar->setZerofillWeeks()
	->render(3, 2011);


$calendar = new \Calendar;
echo '<h2>Modified default headings</h2>';
echo $calendar->setDayHeadings(['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'])
	->setMonthHeadings(['leden','unor','brezen','duben','kveten','cerven','cervenec','srpen','zari','rijen','listopad','prosinec'])
	->render(4, 2011);


$calendar = new \Calendar;
echo '<h2>Headings excluded</h2>';
echo $calendar->setIncludeDayHeadings(FALSE)
	->setIncludeMonthHeadings(FALSE)
	->render(5, 2011);


$calendar = new \Calendar;
echo '<h2>...and week number excluded</h2>';
echo $calendar->setIncludeDayHeadings(FALSE)
	->setIncludeMonthHeadings(FALSE)
	->setIncludeWeekNumbers(FALSE)
	->render(6, 2011);


$calendar = new \Calendar;
echo '<h2>Modified classnames</h2>';
echo $calendar->setTableCssClass('funny')
	->setMonthNameRowCssClass('funny')
	->setWeekNumberCellCssClass('funny')
	->setDayNamesRowCssClass('funny')
	->setOutsideDayCellCssClass('funny')
	->render(7, 2011);


$calendar = new \Calendar;
echo '<h2>Modified classnames</h2>';
echo $calendar->setMonthClasses([7 => 'myMonth'])
	->setDayOfWeekClasses([0 => 'funny', 5 => 'funny'])
	->render(8, 2011);


$calendar = new \Calendar;
echo '<h2>Extra pattern/class outside month scope</h2>';
echo $calendar->setExtraDateClass(new DateTime('2011-08-29'), 'funny')
	->setExtraDateClass(new DateTime('2011-09-10'), 'funny')
	->setExtraDatePattern(new DateTime('2011-10-01'), '<b>X</b>')
	->setExtraDatePattern(new DateTime('2011-09-15'), '<b>X</b>')
	->setApplyExtraPatternsToOutsideDays()
	->render(9, 2011);


echo '<h2>Extra pattern/class outside month scope prohibited</h2>';
echo $calendar->setAddExtraClassesToOutsideDays(FALSE)
	->setApplyExtraPatternsToOutsideDays(FALSE)
	->render(9, 2011);

?>
</body>
</html>
