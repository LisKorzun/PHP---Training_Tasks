#!/usr/bin/php5
<?php

$screenSize = getScreenSize();
define("NEW_LINE", "\n");
define("EXIT_KEY", 27);
define("CALENDAR_WIDTH", 24);
define("PADDING_ROW", ($screenSize['screen']['width'] - CALENDAR_WIDTH) / 2);
define("PADDING_COL", ($screenSize['screen']['height'] - 9) / 2);
define("DAYS_OF_WEEK", " Mo Tu We Th Fr Sa Su");

$today = getdate();
define("TODAY_MONTH", $today['mon']);
$counterMonth = TODAY_MONTH;
define("TODAY_YEAR", $today['year']);
$counterYear = TODAY_YEAR;

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

function printCalendar($m, $y){
    $m = new Month($m, $y);
    $small = ncurses_newwin(9, 26, PADDING_COL, PADDING_ROW);
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
}

ncurses_init();
ncurses_clear();
//$fullscreen = ncurses_newwin ( 0, 0, 0, 0);
printCalendar($counterMonth, $counterYear);
while (true) {
    $pressed = ncurses_getch();
    if ($pressed == EXIT_KEY) {
        break;
    } elseif ($pressed == NCURSES_KEY_RIGHT) {
        $counterMonth++;
       printCalendar($counterMonth, $counterYear);
    }
    elseif ($pressed == NCURSES_KEY_LEFT) {
        $counterMonth--;
        printCalendar($counterMonth, $counterYear);
    }
    elseif ($pressed == NCURSES_KEY_UP) {
        $counterYear++;
        printCalendar($counterMonth, $counterYear);
    }
    elseif ($pressed == NCURSES_KEY_DOWN) {
        $counterYear--;
        printCalendar($counterMonth, $counterYear);
    }
}
ncurses_end();

function getScreenSize()
{
    $settings['screen']['width'] = exec('tput cols');
    $settings['screen']['height'] = exec('tput lines');
    return $settings;
}
