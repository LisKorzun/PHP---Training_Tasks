<?php

Class RandCharacter implements Character
{
    private $x;
    private $y;

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
        ncurses_mvwaddstr(Game::$field, $this->y, $this->x, '*');
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
