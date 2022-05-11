<?php

namespace App;

class Calendar
{
	private $year;
	private $month;
	private $date;
	private $token;

	public function __construct($year, $month, $date, $token)
	{
		$this->year = $year;
		$this->month = $month;
		$this->date = $date;
		$this->token = $token;
	}

	public function getRsv()
	{
		$unixmonth = mktime(0, 0, 0, $this->month, 1, $this->year); //該当月1日のタイムスタンプ
		$prev = date('Y-m', strtotime('-1 month', $unixmonth)); //前月の算出
		$next = date('Y-m', strtotime('+1 month', $unixmonth)); //次月の算出

		$calendar_output = '<caption class="rsv_calendar">' . "\n\t" . '<form action="" method="post">';
		$calendar_output .= "\n\t\t" . '<input class="prev" type="submit" name="calendar[' . $prev . '*' . $this->date . ']" value="&laquo;">';
		$calendar_output .= "\n\t\t" . $this->year . '年' . $this->month . '月';
		$calendar_output .= "\n\t\t" . '<input class="next" type="submit" name="calendar[' . $next . '*' . $this->date . ']" value="&raquo;">';
		$calendar_output .= "\n\t\t" . '<input type="hidden" name="token" value="' . $this->token . '">';
		$calendar_output .= "\n\t</form>\n</caption>";

		echo $calendar_output; //出力
	}
}
