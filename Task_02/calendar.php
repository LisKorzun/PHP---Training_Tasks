#!/usr/bin/php5
<?php

$screenSize = getScreenSize();
define("NEW_LINE", "\n");
define("EXIT_KEY", 27);
define("CALENDAR_WIDTH", 24);
define("CALENDAR_HEIGHT", 9);
define("PADDING_ROW", ($screenSize['screen']['width'] - CALENDAR_WIDTH) / 2);
define("PADDING_COL", ($screenSize['screen']['height'] - (CALENDAR_HEIGHT+4)) / 2);
define("DAYS_OF_WEEK", " Mo Tu We Th Fr Sa Su");
define("CHANGE_MONTH", "To change Month, please, press ← or →");
define("CHANGE_YEAR", "To change Year, please, press ↑ or ↓");
define("PADDING_ROW_LEGEND", ($screenSize['screen']['width'] - max(array(strlen(CHANGE_MONTH) - 3, strlen(CHANGE_YEAR) - 3))) / 2);

$today = getdate();
define("TODAY_MONTH", $today['mon']);
$counterMonth = TODAY_MONTH;
define("TODAY_YEAR", $today['year']);
$counterYear = TODAY_YEAR;

ncurses_init();
ncurses_clear();
ncurses_curs_set(0);
printCalendar($counterMonth, $counterYear);
while (true) {
    $pressed = getch_nonblock(1000000);
    switch ($pressed) {
        case EXIT_KEY:
            break 2;
        case NCURSES_KEY_RIGHT:
            $counterMonth++;
            break;
        case NCURSES_KEY_LEFT:
            $counterMonth--;
            break;
        case NCURSES_KEY_UP:
            $counterYear++;
            break;
        case NCURSES_KEY_DOWN:
            $counterYear--;
            break;
    }
    printCalendar($counterMonth, $counterYear);
}
ncurses_end();

class Month
{
    private $firstDay;
    private $numberOfDays;
    private $month;
    private $year;

    public function __construct($month = TODAY_MONTH, $year = TODAY_YEAR)
    {
        $this->firstDay = date('w', mktime(0, 0, 0, $month, 1, $year));
        $this->numberOfDays = date('t', mktime(0, 0, 0, $month + 1, 0, $year));
        $this->month = $month;
        $this->year = $year;
    }

    public function generateTitle()
    {
        return " ←→ " . date("F", mktime(0, 0, 0, $this->month, 1, 2000)) . ' ' . $this->year . " ↑↓ ";
    }

    public function generateCalendar()
    {
        $col = 7;
        $counter = 1;
        $month = '';
        if ($this->firstDay > 1) {
            $month .= str_repeat('   ', $this->firstDay - 1);
            for ($i = $this->firstDay; $i <= 7; $i++) {
                $month .= '  ' . $counter;
                $counter++;
            }
            $month .= NEW_LINE;
        }
        while ($counter < $this->numberOfDays) {
            for ($i = 0; $i < $col; $i++) {
                if ($counter <= $this->numberOfDays) {
                    ($counter < 10) ? $month .= '  ' . $counter : $month .= ' ' . $counter;
                    $counter++;
                } else {
                    for ($count = $i; $count < $col; $count++) {
                        $month .= '   ';
                    }
                    break 1;
                }
            }
            $month .= NEW_LINE;
        }
        $monthArr = explode(NEW_LINE, rtrim($month));
        return $monthArr;
    }
}

/**
 * Функция необходима для для решения проблем с блокированием потока для ncurses_getch
 * http://php.net/manual/en/function.ncurses-getch.php
 *
 * @param $timeout
 * @return int|null
 */
function getch_nonblock($timeout) {
    $read = array(STDIN);
    $null = null;    // stream_select() uses references, thus variables are necessary for the first 3 parameters
    if(stream_select($read,$null,$null,floor($timeout / 1000000),$timeout % 1000000) != 1) return null;
    return ncurses_getch();
}

function printLegendForCalendar()
{
    $widthLegend = max(array(strlen(CHANGE_MONTH), strlen(CHANGE_YEAR)));
    $legend = ncurses_newwin(4, $widthLegend, PADDING_COL + CALENDAR_HEIGHT, PADDING_ROW_LEGEND);
    ncurses_wborder($legend, 0, 0, 0, 0, 0, 0, 0, 0);
    ncurses_mvwaddstr($legend, 1, 2, CHANGE_MONTH);
    ncurses_mvwaddstr($legend, 2, 2, CHANGE_YEAR);
    ncurses_wrefresh($legend);
}

function printCalendar($m, $y)
{
    $m = new Month($m, $y);
    $small = ncurses_newwin(CALENDAR_HEIGHT, CALENDAR_WIDTH + 2, PADDING_COL, PADDING_ROW);
    ncurses_wborder($small, 0, 0, 0, 0, 0, 0, 0, 0);
    ncurses_attron(NCURSES_A_REVERSE);
    ncurses_mvwaddstr($small, 0, 1, $m->generateTitle());
    ncurses_attroff(NCURSES_A_REVERSE);
    ncurses_mvwaddstr($small, 2, 2, DAYS_OF_WEEK);
    $calendar = $m->generateCalendar();
    for ($i = 0; $i < count($calendar); $i++) {
        $out = $calendar[$i];
        ncurses_mvwaddstr($small, 3 + $i, 2, $out);
    }
    ncurses_wrefresh($small);
    printLegendForCalendar();
}

function getScreenSize()
{
    $settings['screen']['width'] = exec('tput cols');
    $settings['screen']['height'] = exec('tput lines');
    return $settings;
}
