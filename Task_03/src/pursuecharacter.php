<?php

Class PursueCharacter implements Character
{
    public $x;
    public $y;

    public function __construct()
    {
        $this->y = rand(1, Game::$h - 2);
        $this->x = rand(1, Game::$w - 2);
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function moveCharacter()
    {
        $rangeX = [];
        $rangeY = [];
        ncurses_mvwaddstr(Game::$field, $this->y, $this->x, '*');
        for ($i = $this->x - 5; $i < $this->x + 5; $i++) {
            $rangeX[] = $i;
        }
        for ($i = $this->y - 5; $i < $this->y + 5; $i++) {
            $rangeY[] = $i;
        }
        if (in_array(Gamer::$x, $rangeX) AND in_array(Gamer::$y, $rangeY)) {
            if (abs($this->x - Gamer::$x) >= abs($this->y - Gamer::$y)) {
                ($this->x > Gamer::$x) ? $this->x-- : $this->x++;
            } else {
                ($this->y > Gamer::$y) ? $this->y-- : $this->y++;
            }
        } else {
            $rand = rand(1, 4);
            switch ($rand) {
                case 1:
                    if ($this->x == Game::$w - 2) {
                        $this->x--;
                    } else {
                        $this->x++;
                    }
                    break 1;
                case 2:
                    if ($this->x == 1) {
                        $this->x++;
                    } else {
                        $this->x--;
                    }
                    break 1;
                case 3:
                    if ($this->y == Game::$h - 2) {
                        $this->y--;
                    } else {
                        $this->y++;
                    }
                    break 1;
                case 4:
                    if ($this->y == 1) {
                        $this->y++;
                    } else {
                        $this->y--;
                    }
                    break 1;
            }
        }
    }
}
