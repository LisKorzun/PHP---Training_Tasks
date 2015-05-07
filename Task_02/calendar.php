#!/usr/bin/php5
<?php

$screenSize = getScreenSize();

define("NEW_LINE", "\n");
define("CALENDAR_WIDTH", 24);
define("PADDING_ROW", str_repeat(' ',($screenSize['screen']['width']-CALENDAR_WIDTH)/2));
define("PADDING_COL", str_repeat(NEW_LINE,($screenSize['screen']['height']-9)/2));
define("DAYS_OF_WEEK", "Mo Tu We Th Fr Sa Su");

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

    public function generate()
    {
        $col = 7;
        $counter = 1;
        $monthName = date("F", mktime(0, 0, 0, $this->month, 1, 2000));
        $padding = CALENDAR_WIDTH - (strlen($monthName) + 11);
        $month = PADDING_COL.PADDING_ROW. str_repeat(' ', floor($padding/2))."←→ ".$monthName.' '.$this->year." ↑↓".NEW_LINE;
        $month .= PADDING_ROW.str_repeat('.', CALENDAR_WIDTH);
        $month .= NEW_LINE . PADDING_ROW . '| ' . DAYS_OF_WEEK . ' |' . NEW_LINE . PADDING_ROW . '|';
        if ($this->firstDay != 1) {
            $month .= str_repeat('   ', $this->firstDay - 1);
            for ($i = $this->firstDay; $i <= 7; $i++) {
                $month .= '  ' . $counter;
                $counter++;
            }
            $month .= ' |' . NEW_LINE;
        }
        while ($counter < $this->numberOfDays) {
            $month .= PADDING_ROW.'|';
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
            $month .= ' |' . NEW_LINE;
        }
        $month .= PADDING_ROW . str_repeat('.', CALENDAR_WIDTH) . NEW_LINE;
        return $month;
    }
}

$today = getdate();
$todayMonth = $today['mon'];
define("TODAY_MONTH", $todayMonth);
$todayYear = $today['year'];
define("TODAY_YEAR", $todayYear);
$m = new Month(4,2015);

echo `clear`;

print $m->generate();

function getScreenSize() {
        $settings['screen']['width'] = exec('tput cols');
        $settings['screen']['height'] = exec('tput lines');
    return $settings;
}

