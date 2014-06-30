<?php

/**
 * Calendar renderer
 * @author 2012-2014 jsem@hejdav.cz Vladislav Hejda
 * @todo která class dostane přednost - outside nebo special?
 *
 * In patterns use:
 *   %d = Arabic number
 *   %e = Roman number
 */
class Calendar
{

	/** @var int */
	protected $startDay = 0;

	/** @var bool */
	protected $zerofill = FALSE;

	/** @var string */
	protected $dayPattern = '%d';

	/** @var string */
	protected $weekPattern = '%d.';

	/** @var bool */
	protected $outsideDayPattern = FALSE;

	/** @var array */
	protected $extraDatePattern = [];

	/** @var array */
	protected $extraDateClass = [];

	/** @var array */
	protected $dayClasses = [0 => 'sunday'];

	/** @var array */
	protected $monthClasses = [];

	/** @var array */
	protected $monthHeadings = [
		'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
		'September', 'October', 'November', 'December'
	];

	/** @var array */
	protected $dayHeadings = ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'];


	public function __construct()
	{
		$this->setExtraDateClass(new \DateTime, 'today');
	}


	/**
	 * @param int $year     e.g. 2015
	 * @param int $month    1-12
	 */
	public function render($year, $month)
	{
	}


	/**
	 * @param bool $value    whether pad day numbers with leading zero
	 * @return self
	 */
	public function setZerofill($value = TRUE)
	{
		$this->zerofill = (bool) $value;
		return $this;
	}


	/**
	 * @param string $pattern    pattern of the day output
	 * @return self
	 */
	public function setDayPattern($pattern)
	{
		$this->dayPattern = (string) $pattern;
		return $this;
	}


	/**
	 * @param string $pattern    pattern of the day output
	 *   FALSE to do not include week number
	 * @return self
	 */
	public function setWeekPattern($pattern)
	{
		$this->weekPattern = $pattern === FALSE ? FALSE : (string) $pattern;
		return $this;
	}


	/**
	 * @param string $pattern    pattern for days that are off the month scope
	 *   FALSE to use standard day pattern
	 * @return self
	 */
	public function setOutsideDayPattern($pattern)
	{
		$this->outsideDayPattern = $pattern === FALSE ? FALSE : (string) $pattern;
		return $this;
	}


	/**
	 * @param int $dayNumber    first day of week, 0 means Sunday, 6 Saturday
	 * @return self
	 */
	public function setStartDay($dayNumber)
	{
		$this->startDay = $this->validateDayNumber($dayNumber);
		return $this;
	}


	/**
	 * @param int $dayNumber    0 means Sunday
	 * @param string $class
	 * @return self
	 */
	public function setDayClass($dayNumber, $class)
	{
		$this->dayClasses[$this->validateDayNumber($dayNumber)] = (string) $class;
		return $this;
	}


	/**
	 * @param array $dayClasses    indexed by day numbers
	 * @return self
	 */
	public function setDayClasses(array $dayClasses)
	{
		$this->dayClasses = [];
		foreach ($dayClasses as $dayNumber => $class) {
			$this->setDayClass($dayNumber, $class);
		}
		return $this;
	}


	/**
	 * @param array $monthClasses    indexed by month numbers, 0 means January
	 * @return self
	 */
	public function setMonthClasses(array $monthClasses)
	{
		$this->monthClasses = [];
		foreach ($monthClasses as $monthNumber => $class) {
			$this->monthClasses[$this->validateMonthNumber($monthNumber)] = (string) $class;
		}
		return $this;
	}


	/**
	 * @param array $headings
	 * @return self
	 */
	public function setMonthHeadings(array $headings)
	{
		foreach ($headings as $monthNumber => $heading) {
			$this->monthHeadings[$this->validateMonthNumber($monthNumber)] = (string) $heading;
		}
		return $this;
	}


	/**
	 * @param array $headings
	 * @return self
	 */
	public function setDayHeadings(array $headings)
	{
		foreach ($headings as $dayNumber => $heading) {
			$this->dayHeadings[$this->validateDayNumber($dayNumber)] = (string) $heading;
		}
		return $this;
	}


	/**
	 * @param \DateTime $date
	 * @param string $pattern
	 * @return self
	 */
	public function setExtraDatePattern(\DateTime $date, $pattern)
	{
		$this->extraDatePattern[$this->generateStamp($date)] = (string) $pattern;
		return $this;
	}


	/**
	 * @param \DatePeriod $period
	 * @param string $pattern
	 * @return self
	 */
	public function setExtraPeriodPattern(\DatePeriod $period, $pattern)
	{
		$pattern = (string) $pattern;
		foreach ($period as $date) {
			$this->setExtraDatePattern($date, $pattern);
		}
		return $this;
	}


	/**
	 * @param \DateTime $date
	 * @param string $class
	 * @return self
	 */
	public function setExtraDateClass(\DateTime $date, $class)
	{
		$stamp = $this->generateStamp($date);
		if (!isset($this->extraDateClass[$stamp])) {
			$this->extraDateClass[$stamp] = [];
		}
		$this->extraDateClass[$stamp][] = (string) $class;
		return $this;
	}


	/**
	 * @param \DatePeriod $period
	 * @param string $class
	 * @return self
	 */
	public function setExtraPeriodClass(\DatePeriod $period, $class)
	{
		foreach ($period as $date) {
			$this->setExtraDateClass($date, $class);
		}
		return $this;
	}


	// Generuje kalendář měsíce
	public function month($month, $year, $format = 0)
	{
		$this->correctMonth($month, $year);

		// pro snažší použití
		if ($format === true) $format = 1;
		else if (is_int($format)) ++$format;

		// počet sloupců
		if ($this->weekPattern){
			$column_count = 8;
		}
		else {
			$column_count = 7;
		}

		// posun dní
		if ($this->startDay > 0 AND $this->startDay < 7){
			$shift = array();
			for ($i = 0; $i < 7; $i++){
				$newday = $this->startDay +$i;
				if ($newday > 6){
					$newday = $newday -7;
				}
				$shift[$i] = $newday;
			}
		}
		else {
			$shift = range(0,6);
		}


		// dni v kalendáři měsíce stojící před měsícem
		$outdays_count = $this->monthStart($month, $year) - $this->startDay;
		if ($outdays_count < 0){
			$outdays_count += 7;
		}
		$outdays = array();
		for ($i = $outdays_count -1; $i >= 0; $i--){
			$outdays[] = $this->dayCount($month -1, $year) -$i;
		}

		// týdnů v kalendáři měsíce
		$weeks = ceil (($this->dayCount($month, $year) + $outdays_count) / 7);

		// číslo prvního týdne
		if (isset($outdays[0])){
			$week_no = $this->weekNo($outdays[0], $month -1, $year);
		}
		else {
			$week_no = $this->weekNo(1, $month, $year);
		}
		$week_last = false;
		if ($month == 1){
			$week_last = true;
		}

		// vzor dní mimo
		if (false === $this->outsideDayPattern){
			$outside = $this->dayPattern;
		}
		else {
			$outside = $this->outsideDayPattern;
		}


		// VÝSTUP
		$r = '';

		if ($format) $r .= htmlf(0, $format -1);
		$r .= '<table class="calendar">';

		$thead_given = false;

		// název měsíce
		if (isset($this->monthHeadings[$month -1])){
			if (!$thead_given){
				if ($format) $r .= htmlf(1, $format -1);
				$r .= '<thead>';
				$thead_given = true;
			}

			// CSS třída pro měsíc
			if (isset ($this->monthClasses[$month -1])){
				$class = ' '.$this->monthClasses[$month -1];
			}
			else {
				$class = '';
			}
			if ($format) $r .= htmlf(2, $format -1);
			$r .= '<tr class="month'.$class.'">';
			if ($format) $r .= htmlf(3, $format -1);
			$r .= '<td colspan="'.$column_count.'">'.$this->monthHeadings[$month -1].'</td>';

			if ($format) $r .= htmlf(2, $format -1);
			$r .= '</tr>';
		}

		// názvy dnů
		if ($this->dayHeadings){
			if (!$thead_given){
				if ($format) $r .= htmlf(1, $format -1);
				$r .= '<thead>';
				$thead_given = true;
			}

			if ($format) $r .= htmlf(2, $format -1);
			$r .= '<tr class="daynames">';
			// volná pozice nad číslem týdne
			if ($this->weekPattern){
				if ($format) $r .= htmlf(3, $format -1);
				$r .= '<td class="week">&nbsp;</td>';
			}
			// dny
			for($i = 0; $i < 7; $i++){
				if ($format) $r .= htmlf(3, $format -1);
				// CSS třída k názvu dne
				if (isset($this->dayClasses[$shift[$i]])){
					$class = ' class="'.$this->dayClasses[$shift[$i]].'"';
				}
				else {
					$class = '';
				}
				$r .= '<td'.$class.'>'.$this->dayHeadings[$shift[$i]].'</td>';
			}
			if ($format) $r .= htmlf(2, $format -1);
			$r .= '</tr>';
		}

		if ($thead_given){
			if ($format) $r .= htmlf(1, $format -1);
			$r .= '</thead>';
		}

		if ($format) $r .= htmlf(1, $format -1);
		$r .= '<tbody>';

		// kalendář
		$out_filled = false;
		$day = $outday2 = 1;

		// sloupec
		for ($row = 0; $row < $weeks; $row++){
			if ($format) $r .= htmlf(2, $format -1);
			$r .= '<tr>';

			// číslo týdne
			if ($this->weekPattern){
				if ($format) $r .= htmlf(3, $format -1);
				$r .= '<td class="week">'.$this->fillPattern($this->weekPattern, $week_no++).'</td>';
				if ($week_last){
					$week_no = 1;
					$week_last = false;
				}
			}

			// dny mimo
			if (!$out_filled){
				foreach ($outdays as $outday){
					$classes = $this->dayClasses($outday, $month -1, $year);
					$classes[] = 'out';
					if ($format) $r .= htmlf(3, $format -1);
					$r .= '<td class="'.implode(' ', $classes).'">'.$this->fillDay($outside, $outday, $month -1, $year).'</td>';
				}
				$out_filled = true;
				$start_col = $outdays_count;
			}
			else {
				$start_col = 0;
			}

			// řádek
			for ($col = $start_col; $col < 7; $col++){
				if ($day > $this->dayCount($month, $year)){
					$classes = $this->dayClasses($outday2, $month +1, $year);
					$classes[] = 'out';
					if ($format) $r .= htmlf(3, $format -1);
					$r .= '<td class="'.implode(' ', $classes).'">'.$this->fillDay($outside, $outday2, $month +1, $year).'</td>';
					$outday2++;
					continue;
				}
				$classes = $this->dayClasses($day, $month, $year);
				if (count($classes)){
					$classes = ' class="'.implode(' ', $classes).'"';
				}
				else {
					$classes = '';
				}
				if ($format) $r .= htmlf(3, $format -1);
				$r .= '<td'.$classes.'>'.$this->fillDay($this->dayPattern, $day, $month, $year).'</td>';
				++$day;
			}

			if ($format) $r .= htmlf(2, $format -1);
			$r .= '</tr>';
		}

		if ($format) $r .= htmlf(1, $format -1);
		$r .= '</tbody>';

		if ($format) $r .= htmlf(0, $format -1);
		$r .= '</table>';
		if ($format) $r .= "\n";

		return $r;
	}



	/**
	 * @param \DateTime $date
	 * @return string
	 */
	protected function generateStamp(\DateTime $date)
	{
		return $date->format('Ymd');
	}


	/**
	 * @param int $dayNumber
	 * @return int
	 * @throws InvalidArgumentException
	 */
	protected function validateDayNumber($dayNumber)
	{
		$dayNumber = (int) $dayNumber;
		if ($dayNumber < 0 || $dayNumber > 6) {
			throw new InvalidArgumentException("Day number must be an integer between 0 and 6. $dayNumber is not.");
		}
		return $dayNumber;
	}


	/**
	 * @param int $monthNumber
	 * @return int
	 * @throws InvalidArgumentException
	 */
	protected function validateMonthNumber($monthNumber)
	{
		$monthNumber = (int) $monthNumber;
		if ($monthNumber < 0 || $monthNumber > 11) {
			throw new InvalidArgumentException("Month number must be an integer between 0 and 11. $monthNumber is not.");
		}
		return $monthNumber;
	}






	// první den měsíce
	protected $first_day = array();

	// počet dní v měsíci
	protected $day_count = array();


	
	// Generuje kalendář měsíců
	// generuje navíc měsíce za a před
	public function months($month, $year, $next = 0, $prev = 0, $format = 0){
		$r = '';
		for ($i = $month -$prev; $i <= $month +$next; $i++){
			$r .= $this->month($i, $year, $format);
		}
		return $r;
	}
	

	
	
	
	
	

	
	protected function dayClasses($day, $month, $year){
		$this->correctDay($day, $month, $year);
		$mark = $this->dayMark($day, $month, $year);
		if (isset($this->extraDateClass[$mark])){
			return $this->extraDateClass[$mark];
		}
		return array();
	}
	
	protected function fillDay($pattern, $day, $month, $year){
		$this->correctDay($day, $month, $year);
		$mark = $this->dayMark($day, $month, $year);
		if (isset($this->extraDatePattern[$mark])){
			$pattern = $this->extraDatePattern[$mark];
		}
		$res = $this->fillPattern($pattern, $day);
		return $res;
	}
	
	protected function fillPattern($pattern, $number){
		$srch = array('%d');
		$rplc = array($number);

		if (false !== strpos($pattern, '%e')){
			$srch[] = '%e';
			$rplc[] = introman($number);
		}
		
		$res = str_replace($srch, $rplc, $pattern);
		
		return $res;
	}
	
	protected function weekNo($day, $month, $year){
		$this->correctMonth($month, $year);
		$mark = $this->dayMark($day, $month, $year);
		if (isset($this->first_week[$mark])){
			return $this->first_week[$mark];
		}
		$first_week = date('W', mktime(0,0,1,$month,$day,$year));
		$this->first_week[$mark] = $first_week;
		return $first_week;
	}
	
	protected function monthStart($month, $year){
		$mark = $this->dayMark(1, $month, $year);
		if (isset($this->first_day[$mark])){
			return $this->first_day[$mark];
		}
		$first_day = date('w', mktime(0,0,1,$month,1,$year));
		$this->first_day[$mark] = $first_day;
		return $first_day;
	}
	
	protected function makeDays($from, $to){
		$from_day = $this->parseDay($from);
		
		// cybné zadání
		if (!$from_day){
			return array();
		}
		
		if (!is_null($to)){
			$to_day = $this->parseDay($to);
		}
		else {
			$to_day = false;
		}
		
		// rozsah dní
		if ($to_day){
			$days = $this->daysRange($from_day, $to_day);
		}
		else {
			$days = array($from_day);
		}
		
		return $days;
	}
	
	protected function parseDay($daystring){
		// parsování dne, měsíce a roku
		if (preg_match('~^(\\d+)(\\.|/|-)(\\d+)(\\.|/|-)(\\d+)$~', $daystring, $match)){
			$day = (int) $match[1];
			$month = (int) $match[3];
			$year = (int) $match[5];
		}

		// uspěšně vyparsováno
		if (isset($day)){
			// korekce
			$this->correctDay($day, $month, $year);
			
			return array('d' => $day, 'm' => $month, 'y' => $year);
		}
		
		return false;
	}
	
	// korekce dne
	protected function correctDay(& $day, & $month, & $year){
		while ($day < 1){
			$day = $day + $this->dayCount($month -1, $year);
			--$month;
		}
		while ($day > $this->dayCount($month, $year)){
			$day = $day - $this->dayCount($month, $year);
			++$month;
		}
		$this->correctMonth($month, $year);
	}
	
	// korekce měsíce
	protected function correctMonth(& $month, & $year){
		while ($month < 1){
			$month = $month + 12;
			--$year;
		}
		while ($month > 12){
			$month = $month - 12;
			++$year;
		}
	}
	
	// počet dní v měsíci
	protected function dayCount($month, $year){
		$this->correctMonth($month, $year);
		
		// byl-li již počet dní načten
		$mark = $this->dayMark(1, $month, $year);
		if (isset($this->day_count[$mark])){
			return $this->day_count[$mark];
		}
		
		switch ($month){
			case 1: case 3: case 5: case 7: case 8: case 10: case 12:
				$days = 31;
			break;
			case 4: case 6: case 9: case 11:
				$days = 30;
			break;
			case 2:
				$days = date('t', mktime(0,0,1,2,1,$year));
			break;
		}
		$this->day_count[$mark] = $days;
		return $days;
	}
	
	// unikátní označení dne 
	protected function dayMark($day, $month, $year){
		$mark = fillzero($day, 2) . fillzero($month, 2) . fillzero($year, 4);
		return (int) $mark;
	}
	
	// rozsah dní
	protected function daysRange($from, $to){
		// přehození od a do
		if (
			($to['y'] < $from['y'])
			OR
			($to['y'] == $from['y'] AND $to['m'] < $from['m'])
			OR
			($to['y'] == $from['y'] AND $to['m'] == $from['m'] AND $to['d'] < $from['d'])
		){
			swap ($from, $to);
		}

		$range = array($from);
		$now = $from;
		// výpočet dní
		while (true){
			++$now['d'];
			// korekce
			if ($now['d'] > $this->dayCount($now['m'], $now['y'])){
				$now['d'] = 1;
				++$now['m'];
				if ($now['m'] > 12){
					$now['m'] = 1;
					++$now['y'];
				}
			}
			$range[] = $now;
			
			// stop u konce
			if ($to['d'] == $now['d'] AND $to['m'] == $now['m'] AND $to['y'] == $now['y']){
				break;
			}
		}
		
		return $range;
	}
}
