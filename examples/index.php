<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>PHP Calendar</title>
<style>
* { margin: 0; padding: 0; }
body { font-family: Arial; }
h1, p { margin: 15px; }
code { background: #fff8d6; display: block; margin: 20px 15px; font-family: "Courier New"; }
table { border-spacing: 0; margin: 15px; border: #ffc571 1px solid; }
td { border: #e1e1e1 1px solid; padding: 1px 2px; background: #f7ffe4; }
h2 { margin: 20px 0 0 15px; }
h2 a[href] { color: #ccc; text-decoration: none; }
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

<p>In all patterns use <strong>%d</strong> for Arabic numeral, <strong>%e</strong> for Roman numeral.</p>

<?php

require __DIR__ . '/../src/Calendar.php';

function show($code) {
	echo "\n\n" . highlight_string("<?php\n" . $code, TRUE) . "\n\n";
}


echo '<h2><a href="#common-use">#</a> <a name="common-use">Common use</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar->render(4, 2010);
EOD;
show($code);
eval($code);


echo '<h2><a href="#change-day-pattern">#</a> <a name="change-day-pattern">Change day pattern (Roman numerals - %e)</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setDayPattern('%e.')
	->render(5, 2010);
EOD;
show($code);
eval($code);


echo '<h2><a href="#change-outside-days-pattern">#</a> <a name="change-outside-days-pattern">Change outside days pattern</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setOutsideDayPattern('&nbsp;')
	->render(6, 2010);
EOD;
show($code);
eval($code);


echo '<h2><a href="#change-week-starting-day">#</a> <a name="change-week-starting-day">Change week starting day</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setStartingDay(\Calendar::MONDAY)
	->render(7, 2010);
EOD;
show($code);
eval($code);


echo '<h2><a href="#change-week-pattern">#</a> <a name="change-week-pattern">Change week pattern (Roman numerals)</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setWeekPattern('%e.')
	->render(8, 2010);
EOD;
show($code);
eval($code);


echo '<h2><a href="#pad-day-numbers-with-leading-zero">#</a> <a name="pad-day-numbers-with-leading-zero">Pad day numbers with leading zero</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setZerofillDays()
	->render(9, 2010);
EOD;
show($code);
eval($code);


echo '<h2><a href="#add-extra-class-for-certain-dates">#</a> <a name="add-extra-class-for-certain-dates">Add extra class for certain dates</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setExtraDateClass(new \DateTime('2010-10-15'), 'extra')
	->setExtraDateClass(new \DateTime('2010-10-25'), 'extra')
	->render(10, 2010);
EOD;
show($code);
eval($code);


echo '<h2><a href="#add-extra-class-for-certain-period">#</a> <a name="add-extra-class-for-certain-period">Add extra class for certain period</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
$period = new \DatePeriod(new \DateTime('2010-11-11'), new DateInterval('P1D'), 8);
echo $calendar->setExtraPeriodClass($period, 'extra')
	->render(11, 2010);
EOD;
show($code);
eval($code);


echo '<h2><a href="#use-extra-pattern-for-certain-date-period">#</a> <a name="use-extra-pattern-for-certain-date-period">Use extra pattern for certain date/period</a></h2>';
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


echo '<h2><a href="#week-of-last-year-in-new-one">#</a> <a name="week-of-last-year-in-new-one">Week of last year in new one</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar->render(1, 2011);
EOD;
show($code);
eval($code);



echo '<h2><a href="#how-extra-classes-overlaying">#</a> <a name="how-extra-classes-overlaying">How extra classes overlaying</a></h2>';
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


echo '<h2><a href="#pad-week-numbers-with-leading-zero">#</a> <a name="pad-week-numbers-with-leading-zero">Pad week numbers with leading zero</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setZerofillWeeks()
	->render(3, 2011);
EOD;
show($code);
eval($code);


echo '<h2><a href="#change-day-month-default-headings">#</a> <a name="change-day-month-default-headings">Change day / month default headings</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setDayHeadings(['ne', 'po', 'ut', 'st', 'ct', 'pa', 'so'])
	->setMonthHeadings(['leden','unor','brezen','duben','kveten','cerven','cervenec','srpen','zari','rijen','listopad','prosinec'])
	->render(4, 2011);
EOD;
show($code);
eval($code);


echo '<h2><a href="#exclude-headings">#</a> <a name="exclude-headings">Exclude headings</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setIncludeDayHeadings(FALSE)
	->setIncludeMonthHeadings(FALSE)
	->render(5, 2011);
EOD;
show($code);
eval($code);


echo '<h2><a href="#exclude-week-numbers">#</a> <a name="exclude-week-numbers">Exclude week numbers</a></h2>';
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


echo '<h2><a href="#change-classnames-of-table-structure">#</a> <a name="change-classnames-of-table-structure">Change classnames of table structure</a></h2>';
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


echo '<h2><a href="#add-table-classname-for-certain-month-add-classname-for-certain-day-of-week">#</a> <a name="add-table-classname-for-certain-month-add-classname-for-certain-day-of-week">Add table classname for certain month + add classname for certain day of week (0 = Sunday)</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setMonthClasses([7 => 'myMonth'])
	->setDayOfWeekClasses([0 => 'funny', 5 => 'funny'])
	->render(8, 2011);
EOD;
show($code);
eval($code);


echo '<h2><a href="#use-extra-pattern-class-outside-month-scope">#</a> <a name="use-extra-pattern-class-outside-month-scope">Use extra pattern / class outside month scope</a></h2>';
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


echo '<h2><a href="#forbid-using-extra-pattern-class-outside-month-scope">#</a> <a name="forbid-using-extra-pattern-class-outside-month-scope">Forbid using extra pattern / class outside month scope</a></h2>';
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
echo '<h2><a href="#add-week-number-heading">#</a> <a name="add-week-number-heading">Add week number heading</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->setWeekNumbersHeading('week')
	->render(10, 2011);
EOD;
show($code);
eval($code);



$calendar = new \Calendar;
echo '<h2><a href="#do-not-render-html-indention">#</a> <a name="do-not-render-html-indention">Do not render HTML indention (see source code)</a></h2>';
$code = <<<'EOD'
$calendar = new \Calendar;
echo $calendar
	->render(11, 2011, FALSE);
EOD;
show($code);
eval($code);



$calendar = new \Calendar;
echo '<h2><a href="#add-html-indention-offset-change-indention-string">#</a> <a name="add-html-indention-offset-change-indention-string">Add HTML indention offset, change indention string (see source code)</a></h2>';
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
