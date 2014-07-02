<?php

/**
 * Calendar renderer
 * @author 2012-2014 jsem@hejdav.cz Vladislav Hejda
 * @todo která class dostane přednost - outside nebo special?
 * @todo days callbacks
 * @todo aggregate some vars
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
	protected $zerofillDays = FALSE;

	/** @var bool */
	protected $zerofillWeeks = FALSE;

	/** @var string */
	protected $dayPattern = '%d';

	/** @var bool */
	protected $includeWeekNumbers = TRUE;

	/** @var string */
	protected $weekPattern = '%d.';

	/** @var bool */
	protected $outsideDayPattern = NULL;

	/** @var array */
	protected $extraDatePattern = [];

	/** @var array */
	protected $extraDateClass = [];

	/** @var array */
	protected $dayClasses = [0 => 'sunday'];

	/** @var array */
	protected $monthClasses = [];

	/** @var bool */
	protected $includeMonthHeadings = TRUE;

	/** @var array */
	protected $monthHeadings = [
		'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
		'September', 'October', 'November', 'December'
	];

	/** @var bool */
	protected $includeDayHeadings = TRUE;

	/** @var array */
	protected $dayHeadings = ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'];

	/** @var string */
	protected $tableClass = 'calendar',
		$monthNameRowClass = 'month',
		$dayNamesRowClass = 'daynames',
		$weekNumberCellClass = 'week',
		$outsideDayCellClass = 'outday';

	/** @var bool */
	protected $addExtraClassesToOutsideDays = TRUE,
		$applyExtraPatternsToOutsideDays = FALSE;


	private $monthDaysCount, $lastMonthDaysCount, $columnCount, $shift, $daysBefore, $weekCount, $firstWeekNo,
		$startsWithLastWeek, $indent;


	public function __construct()
	{
		$this->setExtraDateClass(new \DateTime, 'today');
	}


	/**
	 * @param bool $value    whether pad day numbers with leading zero
	 * @return self
	 */
	public function setZerofillDays($value = TRUE)
	{
		$this->zerofillDays = (bool) $value;
		return $this;
	}


	/**
	 * @param bool $value    whether pad week numbers with leading zero
	 * @return self
	 */
	public function setZerofillWeeks($value = TRUE)
	{
		$this->zerofillWeeks = (bool) $value;
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
	 * @param bool $value
	 * @return self
	 */
	public function setIncludeWeekNumbers($value = TRUE)
	{
		$this->includeWeekNumbers = (bool) $value;
		return $this;
	}


	/**
	 * @param string $pattern    pattern of the day output
	 * @return self
	 */
	public function setWeekPattern($pattern)
	{
		$this->weekPattern = (string) $pattern;
		return $this;
	}


	/**
	 * @param string $pattern    pattern for days that are off the month scope
	 *   NULL to use standard day pattern
	 * @return self
	 */
	public function setOutsideDayPattern($pattern)
	{
		$this->outsideDayPattern = $pattern === NULL ? NULL : (string) $pattern;
		return $this;
	}


	/**
	 * @param int $dayNumber    first day of week, 0 means Sunday, 6 Saturday
	 * @return self
	 */
	public function setStartDay($dayNumber)
	{
		$this->startDay = self::validateDayNumber($dayNumber);
		return $this;
	}


	/**
	 * @param int $dayNumber    0 means Sunday
	 * @param string $class
	 * @return self
	 */
	public function setDayOfWeekClass($dayNumber, $class)
	{
		$this->dayClasses[self::validateDayNumber($dayNumber)] = (string) $class;
		return $this;
	}


	/**
	 * @param array $dayClasses    indexed by day numbers
	 * @return self
	 */
	public function setDayOfWeekClasses(array $dayClasses)
	{
		$this->dayClasses = [];
		foreach ($dayClasses as $dayNumber => $class) {
			$this->setDayOfWeekClass($dayNumber, $class);
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
			$this->monthClasses[self::validateMonthNumber($monthNumber)] = (string) $class;
		}
		return $this;
	}


	/**
	 * @param bool $value
	 * @return self
	 */
	public function setIncludeMonthHeadings($value = TRUE)
	{
		$this->includeMonthHeadings = (bool) $value;
		return $this;
	}


	/**
	 * @param array $headings
	 * @return self
	 */
	public function setMonthHeadings(array $headings)
	{
		foreach ($headings as $monthNumber => $heading) {
			$this->monthHeadings[self::validateMonthNumber($monthNumber)] = (string) $heading;
		}
		return $this;
	}


	/**
	 * @param bool $value
	 * @return self
	 */
	public function setIncludeDayHeadings($value = TRUE)
	{
		$this->includeDayHeadings = (bool) $value;
		return $this;
	}


	/**
	 * @param array $headings
	 * @return self
	 */
	public function setDayHeadings(array $headings)
	{
		foreach ($headings as $dayNumber => $heading) {
			$this->dayHeadings[self::validateDayNumber($dayNumber)] = (string) $heading;
		}
		return $this;
	}


	/**
	 * @param string $class
	 * @return self
	 */
	public function setTableClass($class)
	{
		$this->tableClass = (string) $class;
		return $this;
	}


	/**
	 * @param string $class
	 * @return self
	 */
	public function setMonthNameRowClass($class)
	{
		$this->monthNameRowClass = (string) $class;
		return $this;
	}


	/**
	 * @param string $class
	 * @return self
	 */
	public function setDayNamesRowClass($class)
	{
		$this->dayNamesRowClass = (string) $class;
		return $this;
	}


	/**
	 * @param string $class
	 * @return self
	 */
	public function setWeekNumberCellClass($class)
	{
		$this->weekNumberCellClass = (string) $class;
		return $this;
	}


	/**
	 * @param string $class
	 * @return self
	 */
	public function setOutsideDayCellClass($class)
	{
		$this->outsideDayCellClass = (string) $class;
		return $this;
	}


	/**
	 * @param \DateTime $date
	 * @param string $pattern
	 * @return self
	 */
	public function setExtraDatePattern(\DateTime $date, $pattern)
	{
		$this->extraDatePattern[self::generateStamp($date)] = (string) $pattern;
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
		$stamp = self::generateStamp($date);
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


	/**
	 * @param bool $value
	 * @return self
	 */
	public function setAddExtraClassesToOutsideDays($value = TRUE)
	{
		$this->addExtraClassesToOutsideDays = (bool) $value;
		return $this;
	}


	/**
	 * @param bool $value
	 * @return self
	 */
	public function setApplyExtraPatternsToOutsideDays($value = TRUE)
	{
		$this->applyExtraPatternsToOutsideDays = (bool) $value;
		return $this;
	}


	/**
	 * @param int $month    1-12
	 * @param int $year     e.g. 2015
	 * @param int|FALSE $indentOffset
	 * @param string $indentString
	 * @return string
	 */
	public function render($month, $year, $indentOffset = 0, $indentString = "\t")
	{
		self::correctMonth($month, $year);

		$this->monthDaysCount = $this->calculateMonthDaysCount($month, $year);
		$this->lastMonthDaysCount = $this->calculateMonthDaysCount($month -1, $year);

		$this->columnCount = 7;
		if ($this->includeWeekNumbers){
			++$this->columnCount;
		}

		$this->shift = $this->calculateDaysShift();
		$this->daysBefore = $this->calculateDaysBefore($month, $year);
		$this->weekCount = $this->calculateWeekCount(count($this->daysBefore));
		$this->startsWithLastWeek = $month === 1;

		if (count($this->daysBefore)) {
			$date = $this->createDate($this->daysBefore[0], $month -1, $year);
		} else {
			$date = $this->createDate(1, $month, $year);
		}
		$this->firstWeekNo = $this->calculateWeekNumber($date);

		$this->indent = function ($level) use ($indentOffset, $indentString) {
			if ($indentOffset === FALSE) {
				return '';
			}
			return "\n" . str_repeat($indentString, $level + $indentOffset);
		};

		$output = $this->build($month, $year);
		if ($indentOffset !== FALSE) {
			$output .= "\n";
		}

		return $output;
	}


	protected function createDate($day, $month, $year)
	{
		$this->correctMonth($month, $year);
		return new \DateTime("$year-$month-$day");
	}


	protected function calculateDaysShift()
	{
		// start day shift
		if ($this->startDay > 0) {
			$shift = [];
			for ($i = 0; $i < 7; $i++) {
				$dayShift = $this->startDay + $i;
				if ($dayShift > 6){
					$dayShift = $dayShift -7;
				}
				$shift[$i] = $dayShift;
			}
			return $shift;
		} else {
			return range(0,6);
		}
	}


	/**
	 * Days outside month scope, left side.
	 */
	protected function calculateDaysBefore($month, $year)
	{
		$daysCount = $this->calculateFirstMonthDay($month, $year) - $this->startDay;
		if ($daysCount < 0) {
			$daysCount += 7;
		}
		$days = [];
		for ($i = $daysCount -1; $i >= 0; $i--) {
			$days[] = $this->lastMonthDaysCount -$i;
		}
		return $days;
	}


	protected function calculateWeekCount($daysBeforeCount)
	{
		return ceil(($this->monthDaysCount + $daysBeforeCount) / 7);
	}


	protected function calculateWeekNumber(\DateTime $date)
	{
		return (int) $date->format('W');
	}


	protected function calculateFirstMonthDay($month, $year)
	{
		$date = $this->createDate(1, $month, $year);
		return $date->format('w');
	}


	protected function calculateMonthDaysCount($month, $year)
	{
		$this->correctMonth($month, $year);
		return cal_days_in_month(CAL_GREGORIAN, $month, $year);
	}


	protected function getDateClasses(\DateTime $date)
	{
		$stamp = self::generateStamp($date);
		if (isset($this->extraDateClass[$stamp])){
			return $this->extraDateClass[$stamp];
		}
		return [];
	}


	protected function applyPattern($pattern, \DateTime $date, $useExtra = TRUE)
	{
		if ($useExtra) {
			$stamp = self::generateStamp($date);
			if (isset($this->extraDatePattern[$stamp])) {
				$pattern = $this->extraDatePattern[$stamp];
			}
		}
		return $this->replaceDelegates($pattern, $date->format($this->zerofillDays ? 'd' : 'j'));
	}


	protected function replaceDelegates($pattern, $number)
	{
		$search = ['%d'];
		$replace = [$number];

		if (strpos($pattern, '%e') !== FALSE) {
			$search[] = '%e';
			$replace[] = self::intToRoman($number);
		}

		return str_replace($search, $replace, $pattern);
	}


	/**
	 * @param \DateTime $date
	 * @return string
	 */
	protected static function generateStamp(\DateTime $date)
	{
		return $date->format('Ymd');
	}


	/**
	 * @param int $dayNumber
	 * @return int
	 * @throws InvalidArgumentException
	 */
	protected static function validateDayNumber($dayNumber)
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
	protected static function validateMonthNumber($monthNumber)
	{
		$monthNumber = (int) $monthNumber;
		if ($monthNumber < 0 || $monthNumber > 11) {
			throw new InvalidArgumentException("Month number must be an integer between 0 and 11. $monthNumber is not.");
		}
		return $monthNumber;
	}


	protected static function intToRoman($number)
	{
		static $romanNumerals = [
			'M' => 1000, 'CM' => 900,
			'D' => 500, 'CD' => 400,
			'C' => 100, 'XC' => 90,
			'L' => 50, 'XL' => 40,
			'X' => 10, 'IX' => 9,
			'V' => 5, 'IV' => 4,
			'I' => 1,
		];
		$result = '';
		foreach ($romanNumerals as $key => $val) {
			$result .= str_repeat($key, floor($number / $val));
			$number %= $val;
		}
		return $result;
	}


	protected static function correctMonth(& $month, & $year)
	{
		// todo this can be done without cycle
		while ($month < 1) {
			$month = $month + 12;
			--$year;
		}
		while ($month > 12) {
			$month = $month - 12;
			++$year;
		}
	}


	protected static function zerofill($number, $digitsCount)
	{
		$length = strlen($number);
		$pad = '';
		if ($length < $digitsCount) {
			$pad = str_repeat('0', $digitsCount - $length);
		}
		return $pad . $number;
	}


	/**
	 * @param int $month
	 * @param int $year
	 * @return string
	 */
	private function build($month, $year)
	{
		$indent = $this->indent;

		$classes = [$this->tableClass];
		if (isset($this->monthClasses[$month -1])) {
			$classes[] = $this->monthClasses[$month -1];
		}
		$output = $indent(0) . '<table class="' . implode(' ', $classes) .'">';
		$tableHeadDumped = FALSE;

		// month name
		if ($this->includeMonthHeadings) {
			if (!$tableHeadDumped) {
				$output .= $indent(1) . '<thead>';
				$tableHeadDumped = TRUE;
			}
			$output .= $this->buildMonthHeading($month);
		}

		// day names
		if ($this->includeDayHeadings) {
			if (!$tableHeadDumped) {
				$output .= $indent(1) . '<thead>';
				$tableHeadDumped = TRUE;
			}
			$output .= $this->buildDayHeadings($month);
		}

		if ($tableHeadDumped) {
			$output .= $indent(1) . '</thead>';
		}

		$output .= $indent(1) . '<tbody>';
		$output .= $this->buildBody($month, $year);
		$output .= $indent(1) . '</tbody>';
		$output .= $indent(0) . '</table>';

		return $output;
	}


	private function buildMonthHeading($month)
	{
		$indent = $this->indent;

		$heading = $indent(2) . '<tr class="' . $this->monthNameRowClass;
		$heading .= '">';
		$heading .= $indent(3) . '<td colspan="' . $this->columnCount . '">' . $this->monthHeadings[$month -1] . '</td>';

		$heading .= $indent(2) . '</tr>';
		return $heading;
	}


	private function buildDayHeadings()
	{
		$indent = $this->indent;

		$headings = $indent(2) . '<tr class="' . $this->dayNamesRowClass . '">';

		//  free position over week number
		if ($this->includeWeekNumbers) {
			$headings .= $indent(3) . '<td class="' . $this->weekNumberCellClass . '">&nbsp;</td>';
		}

		// days
		for ($i = 0; $i < 7; $i++) {
			$headings .= $indent(3) . '<td';
			// day of week  class
			if (isset($this->dayClasses[$this->shift[$i]])) {
				$headings .= ' class="' . $this->dayClasses[$this->shift[$i]] . '"';
			}
			$headings .= '>' . $this->dayHeadings[$this->shift[$i]] . '</td>';
		}
		$headings .= $indent(2) . '</tr>';

		return $headings;
	}


	private function buildBody($month, $year)
	{
		$indent = $this->indent;

		$daysBeforeDumped = FALSE;
		$day = $daysAfter = 1;

		if ($this->outsideDayPattern === NULL) {
			$outsideDayPattern = $this->dayPattern;
		} else {
			$outsideDayPattern = $this->outsideDayPattern;
		}

		$body = '';

		for ($rowNo = 0; $rowNo < $this->weekCount; $rowNo++) {
			$body .= $indent(2) . '<tr>';

			// week number
			if ($this->includeWeekNumbers) {
				$weekString = $this->firstWeekNo++;
				$weekString = $this->zerofillWeeks ? self::zerofill($weekString, 2) : $weekString;
				$body .= $indent(3) . '<td class="' . $this->weekNumberCellClass . '">'
					. $this->replaceDelegates($this->weekPattern, $weekString) . '</td>';
				if ($this->startsWithLastWeek) {
					$this->firstWeekNo = 1;
					$this->startsWithLastWeek = FALSE;
				}
			}

			// days off month scope, left side
			if (!$daysBeforeDumped) {
				foreach ($this->daysBefore as $i => $dayBefore) {

					$date = $this->createDate($dayBefore, $month -1, $year);
					if ($this->addExtraClassesToOutsideDays) {
						$classes = $this->getDateClasses($date);
					} else {
						$classes = [];
					}

					$classes[] = $this->outsideDayCellClass;
					if (isset($this->dayClasses[$this->shift[$i]])) {
						$classes[] = $this->dayClasses[$this->shift[$i]];
					}
					$body .= $indent(3) . '<td class="' . implode(' ', $classes).'">'
						. $this->applyPattern($outsideDayPattern, $date, $this->applyExtraPatternsToOutsideDays) . '</td>';
				}
				$daysBeforeDumped = TRUE;
				$startingDay = count($this->daysBefore);
			} else {
				$startingDay = 0;
			}

			// row
			for ($columnNo = $startingDay; $columnNo < 7; $columnNo++) {

				// days off month scope, right side
				if ($day > $this->monthDaysCount) {
					$date = $this->createDate($daysAfter, $month +1, $year);
					if ($this->addExtraClassesToOutsideDays) {
						$classes = $this->getDateClasses($date);
					} else {
						$classes = [];
					}
					$classes[] = $this->outsideDayCellClass;
					if (isset($this->dayClasses[$this->shift[$columnNo]])) {
						$classes[] = $this->dayClasses[$this->shift[$columnNo]];
					}
					$body .= $indent(3) . '<td class="' . implode(' ', $classes).'">'
						. $this->applyPattern($outsideDayPattern, $date, $this->applyExtraPatternsToOutsideDays) . '</td>';
					++$daysAfter;
					continue;
				}

				$date = $this->createDate($day, $month, $year);
				$classes = $this->getDateClasses($date);
				if (isset($this->dayClasses[$this->shift[$columnNo]])) {
					$classes[] = $this->dayClasses[$this->shift[$columnNo]];
				}
				$body .= $indent(3) . '<td';
				if (count($classes)) {
					$body .= ' class="' . implode(' ', $classes) . '"';
				}
				$body .= '>' . $this->applyPattern($this->dayPattern, $date) . '</td>';
				++$day;
			}

			$body .= $indent(2) . '</tr>';
		}

		return $body;
	}

}
