<?php

Class CharacterRand implements Character
{
    private $x;
    private $y;

    public function __construct()
    {
        $this->y = rand(1, 15 - 2);
        $this->x = rand(1, 40 - 2);
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function moveCharacter($h, $w, $gamerX, $gamerY)
    {
        $rand = rand(1, 4);
        switch ($rand) {
            case 1:
                if ($this->x == $w - 2) {
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
                if ($this->y == $h - 2) {
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
