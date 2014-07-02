<?php

/**
 * Calendar renderer
 * @author 2012-2014 jsem@hejdav.cz Vladislav Hejda
 * @todo která class dostane přednost - outside nebo special?
 * @todo days callbacks
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
	protected $tableCssClass = 'calendar',
		$monthNameRowCssClass = 'month',
		$dayNamesRowCssClass = 'daynames',
		$weekNumberCellCssClass = 'week',
		$outsideDayCellCssClass = 'outday';


	private $columnCount, $shift, $daysBefore, $weekCount, $firstWeekNo, $startsWithLastWeek, $indent;


	public function __construct()
	{
		$this->setExtraDateClass(new \DateTime, 'today');
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
	 * @todo add method setIncludeWeekNumbers() instead of FALSE here
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
		$this->startDay = self::validateDayNumber($dayNumber);
		return $this;
	}


	/**
	 * @param int $dayNumber    0 means Sunday
	 * @param string $class
	 * @return self
	 * @todo asi by měl třídu dostat každý den, ne jen hlavička
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
	 * @todo CSS třída by se víc hodila u table nuž u řádku month
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
	 * @todo u ostatních tříd který určujou CSS class taky nemám v názvu "Css"
	 */
	public function setTableCssClass($class)
	{
		$this->tableCssClass = (string) $class;
		return $this;
	}


	/**
	 * @param string $class
	 * @return self
	 */
	public function setMonthNameRowCssClass($class)
	{
		$this->monthNameRowCssClass = (string) $class;
		return $this;
	}


	/**
	 * @param string $class
	 * @return self
	 */
	public function setDayNamesRowCssClass($class)
	{
		$this->dayNamesRowCssClass = (string) $class;
		return $this;
	}


	/**
	 * @param string $class
	 * @return self
	 */
	public function setWeekNumberCellCssClass($class)
	{
		$this->weekNumberCellCssClass = (string) $class;
		return $this;
	}


	/**
	 * @param string $class
	 * @return self
	 */
	public function setOutsideDayCellCssClass($class)
	{
		$this->outsideDayCellCssClass = (string) $class;
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
	 * @param int $month    1-12
	 * @param int $year     e.g. 2015
	 * @param int|FALSE $indentOffset
	 * @param string $indentString
	 * @return string
	 */
	public function render($month, $year, $indentOffset = 0, $indentString = "\t")
	{
		self::correctMonth($month, $year);

		$this->columnCount = 7;
		if ($this->weekPattern !== FALSE){
			++$this->columnCount;
		}

		$this->shift = $this->calculateDaysShift();
		$this->daysBefore = $this->calculateDaysBefore($month, $year);
		$this->weekCount = $this->calculateWeekCount($month, $year, count($this->daysBefore));
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
			$days[] = $this->calculateMonthDaysCount($month -1, $year) -$i;
		}
		return $days;
	}


	protected function calculateWeekCount($month, $year, $daysBeforeCount)
	{
		return ceil(($this->calculateMonthDaysCount($month, $year) + $daysBeforeCount) / 7);
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


	/**
	 * @todo everything must be lazy
	 */
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


	protected function applyPattern($pattern, \DateTime $date)
	{
		$stamp = self::generateStamp($date);
		if (isset($this->extraDatePattern[$stamp])){
			$pattern = $this->extraDatePattern[$stamp];
		}
		return $this->replaceDelegates($pattern, $date->format($this->zerofill ? 'd' : 'j'));
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


	/**
	 * @param int $month
	 * @param int $year
	 * @return string
	 */
	private function build($month, $year)
	{
		$indent = $this->indent;

		$output = $indent(0) . '<table class="' . $this->tableCssClass .'">';
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

		$heading = $indent(2) . '<tr class="' . $this->monthNameRowCssClass;
		// month CSS class
		if (isset($this->monthClasses[$month -1])) {
			$heading .= ' ' . $this->monthClasses[$month -1];
		}
		$heading .= '">';
		$heading .= $indent(3) . '<td colspan="' . $this->columnCount . '">' . $this->monthHeadings[$month -1] . '</td>';

		$heading .= $indent(2) . '</tr>';
		return $heading;
	}


	private function buildDayHeadings()
	{
		$indent = $this->indent;

		$headings = $indent(2) . '<tr class="' . $this->dayNamesRowCssClass . '">';

		//  free position over week number
		if ($this->weekPattern !== FALSE) {
			$headings .= $indent(3) . '<td class="' . $this->weekNumberCellCssClass . '">&nbsp;</td>';
		}

		// days
		for ($i = 0; $i < 7; $i++) {
			$headings .= $indent(3) . '<td';
			// day of week CSS class
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

		if ($this->outsideDayPattern === FALSE) {
			$outsideDayPattern = $this->dayPattern;
		} else {
			$outsideDayPattern = $this->outsideDayPattern;
		}

		$body = '';

		for ($rowNo = 0; $rowNo < $this->weekCount; $rowNo++) {
			$body .= $indent(2) . '<tr>';

			// week number
			if ($this->weekPattern !== FALSE) {
				$body .= $indent(3) . '<td class="' . $this->weekNumberCellCssClass . '">' . $this->replaceDelegates($this->weekPattern, $this->firstWeekNo++) . '</td>';
				if ($this->startsWithLastWeek) {
					$this->firstWeekNo = 1;
					$this->startsWithLastWeek = FALSE;
				}
			}

			// days off month scope
			if (!$daysBeforeDumped) {
				foreach ($this->daysBefore as $dayBefore) {
					$date = $this->createDate($dayBefore, $month -1, $year);
					$classes = $this->getDateClasses($date);
					$classes[] = $this->outsideDayCellCssClass;
					$body .= $indent(3) . '<td class="' . implode(' ', $classes).'">'
						. $this->applyPattern($outsideDayPattern, $date) . '</td>';
				}
				$daysBeforeDumped = TRUE;
				$startingDay = count($this->daysBefore);
			} else {
				$startingDay = 0;
			}

			// row
			for ($columnNo = $startingDay; $columnNo < 7; $columnNo++) {
				if ($day > $this->calculateMonthDaysCount($month, $year)) {
					$date = $this->createDate($daysAfter, $month +1, $year);
					$classes = $this->getDateClasses($date);
					$classes[] = $this->outsideDayCellCssClass;
					$body .= $indent(3) . '<td class="' . implode(' ', $classes).'">'
						. $this->applyPattern($outsideDayPattern, $date) . '</td>';
					++$daysAfter;
					continue;
				}
				$date = $this->createDate($day, $month, $year);
				$classes = $this->getDateClasses($date);
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
