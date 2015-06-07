<?php

Class Utils
{
    /**
     * Функция необходима для для решения проблем с блокированием потока для ncurses_getch
     * http://php.net/manual/en/function.ncurses-getch.php
     *
     * @param $timeout
     * @return int|null
     */
    static public function getch_nonblock($timeout)
    {
        $read = array(STDIN);
        $null = null;    // stream_select() uses references, thus variables are necessary for the first 3 parameters
        if (stream_select($read, $null, $null, floor($timeout / 1000000), $timeout % 1000000) != 1) return null;
        return ncurses_getch();
    }
}
