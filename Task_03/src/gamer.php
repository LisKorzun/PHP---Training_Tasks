<?php

Class Gamer
{
    private $x;
    private $y;
    private $counter = 0;
    private $h;
    private $w;
    private $constantMotion = true;
    private $liquid = false;

    public function __construct($h, $w)
    {
        $this->y = floor($h / 2);
        $this->x = floor($w / 2);
        $this->h = $h;
        $this->w = $w;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getCounter()
    {
        return $this->counter;
    }

    public function setConstantMotion($constantMotion)
    {
        $this->constantMotion = $constantMotion;
    }

    public function setLiquid($liquid)
    {
        $this->liquid = $liquid;
    }

    public function increaseCounter()
    {
        $this->counter++;
    }

    public function checkRightBorder(&$flag)
    {
        if ($this->x == $this->w - 2) {
            if ($this->liquid) {
                $flag = XRIGHT;
                $this->x = 1;
            } else {
                $flag = XLEFT;
                ($this->constantMotion) ? $this->x-- : $this->x;
            }
        } else {
            $flag = XRIGHT;
            $this->x++;
        }
        return $this->x;
    }

    public function checkLeftBorder(&$flag)
    {
        if ($this->x == 1) {
            if ($this->liquid) {
                $flag = XLEFT;
                $this->x = $this->w - 2;
            } else {
                $flag = XRIGHT;
                ($this->constantMotion) ? $this->x++ : $this->x;
            }
        } else {
            $flag = XLEFT;
            $this->x--;
        }
        return $this->x;
    }

    public function checkTopBorder(&$flag)
    {
        if ($this->y == 1) {
            if ($this->liquid) {
                $flag = YUP;
                $this->y = $this->h - 2;
            } else {
                $flag = YDOWN;
                ($this->constantMotion) ? $this->y++ : $this->y;
            }
        } else {
            $flag = YUP;
            $this->y--;
        }
        return $this->y;
    }

    public function checkBottomBorder(&$flag)
    {
        if ($this->y == $this->h - 2) {
            if ($this->liquid) {
                $flag = YDOWN;
                $this->y = 1;
            } else {
                $flag = YUP;
                ($this->constantMotion) ? $this->y-- : $this->y;
            }
        } else {
            $flag = YDOWN;
            $this->y++;
        }
        return $this->y;
    }
}
