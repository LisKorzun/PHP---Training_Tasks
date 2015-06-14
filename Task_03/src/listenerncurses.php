<?php

Class ListenerNcurses implements Listener
{
    const LEFT_KEY = NCURSES_KEY_LEFT;
    const RIGHT_KEY = NCURSES_KEY_RIGHT;
    const UP_KEY = NCURSES_KEY_UP;
    const DOWN_KEY = NCURSES_KEY_DOWN;

    public function controlUserAction(array $keyForControl, $params = false)
    {
        $pressedKea = NULL;
        while (true) {
            $pressed = ($params == true) ? $this->getch_nonblock(1000000) : ncurses_getch();
                foreach($keyForControl as $kea){
                    if ($pressed == $kea){
                        $pressedKea = $kea;
                        break 2;
                    }
                }
        }
        return $pressedKea;
    }

    /**
     * Функция необходима для для решения проблем с блокированием потока для ncurses_getch
     * http://php.net/manual/en/function.ncurses-getch.php
     *
     * @param $timeout
     * @return int|null
     */
    private function getch_nonblock($timeout)
    {
        $read = array(STDIN);
        $null = null;    // stream_select() uses references, thus variables are necessary for the first 3 parameters
        if (stream_select($read, $null, $null, floor($timeout / 1000000), $timeout % 1000000) != 1) return null;
        return ncurses_getch();
    }
}