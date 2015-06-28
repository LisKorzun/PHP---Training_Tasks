<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Calendar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css"
    ">
</head>
<body>
<div class="container" align="center">
    <?php

    $today = getdate();
    define("TODAY_MONTH", $today['mon']);
    define("TODAY_YEAR", $today['year']);

    function getMonth($calendarMarker = '')
    {
        if (isset($_GET['month' . $calendarMarker])) {
            $counterMonth = (int)$_GET['month' . $calendarMarker];
        } else {
            $counterMonth = TODAY_MONTH;
        }
        return $counterMonth;
    }

    function getYear($calendarMarker = '')
    {
        if (isset($_GET['year' . $calendarMarker])) {
            $counterYear = (int)$_GET['year' . $calendarMarker];
        } else {
            $counterYear = TODAY_YEAR;
        }
        return $counterYear;
    }

    function getHeaderHtml($month, $year, $calendarMarker = '')
    {
        $html = '';
        $html .= '<h3>' . date("F Y", mktime(0, 0, 0, $month, 1, $year)) . '</h3><hr>';

        $get = $_GET;
        $monthReduce = ($month - 1 == 0) ? 12 : ($month - 1);
        $get['month' . $calendarMarker] = $monthReduce;
        $link = http_build_query($get);
        $html .= '<a href="?' . $link . '"> ← </a>';

        $html .= '<span> ' . date("F", mktime(0, 0, 0, $month, 1, $year)) . ' </span>';

        $get = $_GET;
        $monthIncrease = ($month + 1 == 12) ? 0 : ($month + 1);
        $get['month' . $calendarMarker] = $monthIncrease;
        $link = http_build_query($get);
        $html .= '<a href="?' . $link . '"> → </a>';

        $html .= ' | ';

        $get = $_GET;
        $yearReduce = $year - 1;
        $get['year' . $calendarMarker] = $yearReduce;
        $link = http_build_query($get);
        $html .= '<a href="?' . $link . '"> ← </a>';

        $html .= '<span> ' . $year . ' </span>';

        $get = $_GET;
        $yearIncrease = $year + 1;
        $get['year' . $calendarMarker] = $yearIncrease;
        $link = http_build_query($get);
        $html .= '<a href="?' . $link . '"> → </a>';

        $html .= '<hr>';
        return $html;
    }

    function getMonthInArray($calendarMarker = '')
    {
        $dayOfMonth = date('t', mktime(0, 0, 0, getMonth($calendarMarker), 1, getYear($calendarMarker)));
        $dayCount = 1;
        $num = 0;
        for ($i = 0; $i < 7; $i++) {
            $dayOfWeek = date('w',
                mktime(0, 0, 0, getMonth($calendarMarker), $dayCount, getYear($calendarMarker)));
            $dayOfWeek = $dayOfWeek - 1;
            if ($dayOfWeek == -1) $dayOfWeek = 6;
            if ($dayOfWeek == $i) {
                $week[$num][$i] = $dayCount;
                $dayCount++;
            } else {
                $week[$num][$i] = "";
            }
        }
        while (true) {
            $num++;
            for ($i = 0; $i < 7; $i++) {
                $week[$num][$i] = $dayCount;
                $dayCount++;
                if ($dayCount > $dayOfMonth) break;
            }
            if ($dayCount > $dayOfMonth) break;
        }
        return $week;
    }

    function getCalendarHtml()
    {
        static $calendarNumber = 0;
        $calendarNumber++;
        $html = '';
        $html .= getHeaderHtml(getMonth($calendarNumber), getYear($calendarNumber), $calendarNumber);
        $week = getMonthInArray($calendarNumber);
        $html .= "<table>";
        for ($j = 0; $j < 7; $j++) {
            $html .= "<tr>";
            for ($i = 0; $i < count($week); $i++) {
                if (!empty($week[$i][$j])) {
                    if ($j == 5 || $j == 6)
                        $html .= "<td><font color=red>" . $week[$i][$j] . "</font></td>";
                    else $html .= "<td>" . $week[$i][$j] . "</td>";
                } else $html .= "<td>&nbsp;</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</table>";
        return $html;
    }

    echo getCalendarHtml();
    echo getCalendarHtml();
    echo getCalendarHtml();
    echo getCalendarHtml();

    ?>
</div>
</body>
</html>
