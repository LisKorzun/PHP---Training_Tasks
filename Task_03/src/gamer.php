<?php

Class Gamer extends Game
{
    const XRIGHT = 'xRight';
    const XLEFT = 'xLeft';
    const YUP = 'yUp';
    const YDOWN = 'yDown';

    public static $y;
    public static $x;
    private $counter = 0;

    public function playGame()
    {
        parent::playGame();
        self::$y = floor(Game::$h / 2);
        self::$x = floor(Game::$w / 2);
        ncurses_mvwaddstr(Game::$field, self::$y, self::$x, '@');
        ncurses_refresh();
        ncurses_wrefresh(Game::$field);
        $this->getInfo(self::$x, self::$y, $this->counter);
        $flag = '';
        for ($i = 0; $i < $this->complexity * 2; $i++) {
            $name = 'character' . $i;
            $$name = ($i < $this->complexity) ? CharacterFactory::create("PursueCharacter") : CharacterFactory::create("RandCharacter");
        }

        while (true) {
            $pressed = ($this->constantMotion) ? Utils::getch_nonblock(1000000) : ncurses_getch();
            switch ($pressed) {
                case EXIT_KEY:
                    break 2;
                case NCURSES_KEY_RIGHT:
                    $this->checkRightBorder($flag);
                    $this->counter++;
                    break;
                case NCURSES_KEY_LEFT:
                    $this->checkLeftBorder($flag);
                    $this->counter++;
                    break;
                case NCURSES_KEY_UP:
                    $this->checkTopBorder($flag);
                    $this->counter++;
                    break;
                case NCURSES_KEY_DOWN:
                    $this->checkBottomBorder($flag);
                    $this->counter++;
                    break;
                default:
                    if ($this->constantMotion) {
                        switch ($flag) {
                            case self::XRIGHT:
                                $this->checkRightBorder($flag);
                                break 2;
                            case self::XLEFT:
                                $this->checkLeftBorder($flag);
                                break 2;
                            case self::YUP:
                                $this->checkTopBorder($flag);
                                break 2;
                            case self::YDOWN:
                                $this->checkBottomBorder($flag);
                                break 2;
                            default:
                                $this->checkTopBorder($flag);
                                break 2;
                        }
                    }
            }
            $this->fillField(Game::$field, Game::$h, Game::$w, '.');
            ncurses_mvwaddstr(Game::$field, self::$y, self::$x, '@');
            for ($i = 0; $i < $this->complexity * 2; $i++) {
                $name = 'character' . $i;
                $$name->moveCharacter();

            }

            $this->getInfo(self::$x, self::$y, $this->counter);
            ncurses_wrefresh(Game::$field);
            for ($i = 0; $i < $this->complexity * 2; $i++) {
                $name = 'character' . $i;
                if (self::$x == $$name->getX() AND self::$y == $$name->getY()) {
                    ncurses_clear();
                    $this->gameOver();
                    break;
                }
            }
        }
        ncurses_end();
    }

    private function checkRightBorder(&$flag)
    {
        if (self::$x == Game::$w - 2) {
            if ($this->liquid) {
                $flag = self::XRIGHT;
                self::$x = 1;
            } else {
                $flag = self::XLEFT;
                ($this->constantMotion) ? self::$x-- : self::$x;
            }
        } else {
            $flag = self::XRIGHT;
            self::$x++;
        }
        return self::$x;
    }

    private function checkLeftBorder(&$flag)
    {
        if (self::$x == 1) {
            if ($this->liquid) {
                $flag = self::XLEFT;
                self::$x = Game::$w - 2;
            } else {
                $flag = self::XRIGHT;
                ($this->constantMotion) ? self::$x++ : self::$x;
            }
        } else {
            $flag = self::XLEFT;
            self::$x--;
        }
        return self::$x;
    }

    private function checkTopBorder(&$flag)
    {
        if (self::$y == 1) {
            if ($this->liquid) {
                $flag = self::YUP;
                self::$y = Game::$h - 2;
            } else {
                $flag = self::YDOWN;
                ($this->constantMotion) ? self::$y++ : self::$y;
            }
        } else {
            $flag = self::YUP;
            self::$y--;
        }
        return self::$y;
    }

    protected function checkBottomBorder(&$flag)
    {
        if (self::$y == Game::$h - 2) {
            if ($this->liquid) {
                $flag = self::YDOWN;
                self::$y = 1;
            } else {
                $flag = self::YUP;
                ($this->constantMotion) ? self::$y-- : self::$y;
            }
        } else {
            $flag = self::YDOWN;
            self::$y++;
        }
        return self::$y;
    }
}
