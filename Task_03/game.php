#!/usr/bin/php5
<?php

define("EXIT_KEY", 27);
define("H_INFO_FIELD", 10);
define("W_INFO_FIELD", 48);

$gamer = new Character();
$gamer->playGame();


Class Game
{
    public static $h = 15;
    public static $w = 40;
    protected $hScreen;
    protected $wScreen;
    protected $infoField;
    public static $field;
    protected $constantMotion = true;
    protected $liquid = false;

    protected function playGame()
    {
        ncurses_init();
        $this->setInitialSettings();
        $this->startInfo();
        ncurses_clear();
        $paddingTop = (($this->hScreen - self::$h) + H_INFO_FIELD) / 2;
        $paddingLeft = ($this->wScreen - self::$w) / 2;
        self::$field = ncurses_newwin(self::$h, self::$w, $paddingTop, $paddingLeft);
        ncurses_wcolor_set(self::$field, 1);
        ncurses_wborder(self::$field, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->fillField(self::$field, self::$h, self::$w, '.');
    }

    protected function fillField($field, $height, $width, $char)
    {
        for ($i = 1; $i < $height - 1; $i++) {
            ncurses_mvwaddstr($field, $i, 1, str_pad($char, $width - 2, $char));
        }
    }

    private function setInitialSettings()
    {
        ncurses_clear();
        ncurses_curs_set(0);
        $this->wScreen = exec('tput cols');
        $this->hScreen = exec('tput lines');
        if (ncurses_has_colors()) {
            ncurses_start_color();
            ncurses_init_pair(1, NCURSES_COLOR_GREEN, NCURSES_COLOR_BLACK);
            ncurses_init_pair(3, NCURSES_COLOR_BLUE, NCURSES_COLOR_BLACK);
        }
    }

    private function startInfo()
    {
        $this->infoField = ncurses_newwin(H_INFO_FIELD, W_INFO_FIELD, 0, ($this->wScreen - W_INFO_FIELD) / 2);
        ncurses_wcolor_set($this->infoField, 3);
        ncurses_wborder($this->infoField, 0, 0, 0, 0, 0, 0, 0, 0);
        ncurses_mvwaddstr($this->infoField, 1, 2, 'Добро пожаловать!');
        ncurses_mvwaddstr($this->infoField, 3, 2, 'Активен режим:');
        ncurses_mvwaddstr($this->infoField, 4, 2, 'твердых стен и постоянного движения');
        ncurses_mvwaddstr($this->infoField, 5, 2, 'Управляйте персонажем стрелками');
        ncurses_mvwaddstr($this->infoField, 6, 2, 'Для старта нажмите ENTER');
        ncurses_mvwaddstr($this->infoField, 7, 2, 'Для редактирования настроек нажмите "пробел"');
        ncurses_refresh();
        ncurses_wrefresh($this->infoField);
        while (true) {
            $pressed = ncurses_getch();
            if ($pressed == 13) {
                break;
            } elseif ($pressed == 32) {
                $this->editSetting();
                break;
            }
        }
    }

    private function editSetting()
    {
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 3, 11, 'Выберите режим стен:');
        ncurses_mvwaddstr($this->infoField, 5, 11, '0 - твердые стены');
        ncurses_mvwaddstr($this->infoField, 6, 11, '1 - проход сквозь стены');
        ncurses_wrefresh($this->infoField);
        while (true) {
            $pressed = ncurses_getch();
            if ($pressed == 48) {
                $this->liquid = false;
                break;
            } elseif ($pressed == 49) {
                $this->liquid = true;
                break;
            }
        }
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 3, 11, 'Выберите режим движения:');
        ncurses_mvwaddstr($this->infoField, 5, 11, '0 - перемещение по нажатию');
        ncurses_mvwaddstr($this->infoField, 6, 11, '1 - постоянное движение');
        ncurses_wrefresh($this->infoField);
        while (true) {
            $pressed = ncurses_getch();
            if ($pressed == 48) {
                $this->constantMotion = false;
                break;
            } elseif ($pressed == 49) {
                $this->constantMotion = true;
                break;
            }
        }
    }

    protected function getInfo($x, $y, $n)
    {
        $wall = ($this->liquid) ? 'прохода сквозь стены' : 'твердых стен';
        $motion = ($this->constantMotion) ? 'постоянного движения' : 'перемещения по нажатию';
        ncurses_wborder($this->infoField, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 1, 2, 'Активен режим:');
        ncurses_mvwaddstr($this->infoField, 2, 2, $wall . ' и ' . $motion);
        ncurses_mvwaddstr($this->infoField, 4, 2, 'Текущая координата x = ' . $x);
        ncurses_mvwaddstr($this->infoField, 5, 2, 'Текущая координата y = ' . $y);
        ncurses_mvwaddstr($this->infoField, 6, 2, 'Количество нажатий клавиш: ' . $n);
        ncurses_mvwaddstr($this->infoField, 7, 2, 'Для выхода из игры нажмите Esc');
        ncurses_wrefresh($this->infoField);
    }
}


Class Gamer extends Game
{
    const XRIGHT = 'xRight';
    const XLEFT = 'xLeft';
    const YUP = 'yUp';
    const YDOWN = 'yDown';

    protected $y;
    protected $x;
    private $counter = 0;

    public function playGame()
    {
        parent::playGame();
        $this->y = floor(Game::$h / 2);
        $this->x = floor(Game::$w / 2);
        ncurses_mvwaddstr(Game::$field, $this->y, $this->x, '@');
        ncurses_refresh();
        ncurses_wrefresh(Game::$field);
        $this->getInfo($this->x, $this->y, $this->counter);
        $flag = '';
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
            ncurses_mvwaddstr(Game::$field, $this->y, $this->x, '@');
            static::moveCharacter();
            $this->getInfo($this->x, $this->y, $this->counter);
            ncurses_wrefresh(Game::$field);
        }
        ncurses_end();
    }

    private function CheckRightBorder(&$flag)
    {
        if ($this->x == Game::$w - 2) {
            if ($this->liquid) {
                $flag = self::XRIGHT;
                $this->x = 1;
            } else {
                $flag = self::XLEFT;
                ($this->constantMotion) ? $this->x-- : $this->x;
            }
        } else {
            $flag = self::XRIGHT;
            $this->x++;
        }
        return $this->x;
    }

    private function checkLeftBorder(&$flag)
    {
        if ($this->x == 1) {
            if ($this->liquid) {
                $flag = self::XLEFT;
                $this->x = Game::$w - 2;
            } else {
                $flag = self::XRIGHT;
                ($this->constantMotion) ? $this->x++ : $this->x;
            }
        } else {
            $flag = self::XLEFT;
            $this->x--;
        }
        return $this->x;
    }

    private function checkTopBorder(&$flag)
    {
        if ($this->y == 1) {
            if ($this->liquid) {
                $flag = self::YUP;
                $this->y = Game::$h - 2;
            } else {
                $flag = self::YDOWN;
                ($this->constantMotion) ? $this->y++ : $this->y;
            }
        } else {
            $flag = self::YUP;
            $this->y--;
        }
        return $this->y;
    }

    protected function checkBottomBorder(&$flag)
    {
        if ($this->y == Game::$h - 2) {
            if ($this->liquid) {
                $flag = self::YDOWN;
                $this->y = 1;
            } else {
                $flag = self::YUP;
                ($this->constantMotion) ? $this->y-- : $this->y;
            }
        } else {
            $flag = self::YDOWN;
            $this->y++;
        }
        return $this->y;
    }

    public static function moveCharacter()
    {
    }
}

Class Character extends Gamer
{
    public static $cx;
    public static $cy;

    public function __construct()
    {
        self::$cy = rand(1, Game::$h - 1);
        self::$cx = rand(1, Game::$w - 1);
    }

    public static function moveCharacter()
    {
        ncurses_mvwaddstr(Game::$field, self::$cy, self::$cx, '*');
        $rand = rand(1, 4);
        switch ($rand) {
            case 1:
                if (self::$cx == Game::$w - 2) {
                    self::$cx--;
                } else {
                    self::$cx++;
                }
                break 1;
            case 2:
                if (self::$cx == 1) {
                    self::$cx++;
                } else {
                    self::$cx--;
                }
                break 1;
            case 3:
                if (self::$cy == Game::$h - 2) {
                    self::$cy--;
                } else {
                    self::$cy++;
                }
                break 1;
            case 4:
                if (self::$cy == 1) {
                    self::$cy++;
                } else {
                    self::$cy--;
                }
                break 1;
        }
    }
}


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
