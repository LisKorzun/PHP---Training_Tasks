#!/usr/bin/php5
<?php

define("EXIT_KEY", 27);
define("H_INFO_FIELD", 10);
define("W_INFO_FIELD", 48);

$gamer = new Gamer();
$gamer->playGame();

Class Game
{
    protected $h = 15;
    protected $w = 40;
    protected $hScreen;
    protected $wScreen;
    protected $infoField;
    protected $field;
    protected $constantMotion = true;
    protected $liquid = false;

    protected function playGame()
    {
        ncurses_init();
        $this->setInitialSettings();
        $this->startInfo();
        ncurses_clear();
        $this->field = ncurses_newwin($this->h, $this->w, PADDING_TOP, PADDING_LEFT);
        ncurses_wcolor_set($this->field, 1);
        ncurses_wborder($this->field, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->fillField($this->field, $this->h, $this->w, '.');
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
        define('PADDING_TOP', (($this->hScreen - $this->h) + H_INFO_FIELD) / 2);
        define('PADDING_LEFT', ($this->wScreen - $this->w) / 2);
    }

    public function startInfo()
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

    public function editSetting()
    {
        $this->fillField($this->infoField, 9, W_INFO_FIELD - 1, ' ');
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
        $this->fillField($this->infoField, 9, W_INFO_FIELD - 1, ' ');
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
        $this->fillField($this->infoField, 9, W_INFO_FIELD - 1, ' ');
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
    private $y;
    private $x;
    private $counter = 0;

    public function playGame()
    {
        parent::playGame();
        $this->y = floor($this->h / 2);
        $this->x = floor($this->w / 2);
        ncurses_mvwaddstr($this->field, $this->y, $this->x, '@');
        ncurses_refresh();
        ncurses_wrefresh($this->field);
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
                            case 'xRight':
                                $this->checkRightBorder($flag);
                                break 2;
                            case 'xLeft':
                                $this->checkLeftBorder($flag);
                                break 2;
                            case 'yUp':
                                $this->checkTopBorder($flag);
                                break 2;
                            case 'yDown':
                                $this->checkBottomBorder($flag);
                                break 2;
                            default:
                                $this->checkTopBorder($flag);
                                break 2;
                        }
                    }
            }
            $this->fillField($this->field, $this->h, $this->w, '.');
            ncurses_mvwaddstr($this->field, $this->y, $this->x, '@');
            $this->getInfo($this->x, $this->y, $this->counter);
            ncurses_wrefresh($this->field);
        }
        ncurses_end();
    }

    private function CheckRightBorder(&$flag)
    {
        if ($this->x == $this->w - 2) {
            if ($this->liquid) {
                $flag = 'xRight';
                $this->x = 1;
            } else {
                $flag = 'xLeft';
                ($this->constantMotion) ? $this->x-- : $this->x;
            }
        } else {
            $flag = 'xRight';
            $this->x++;
        }
        return $this->x;
    }

    private function checkLeftBorder(&$flag)
    {
        if ($this->x == 1) {
            if ($this->liquid) {
                $flag = 'xLeft';
                $this->x = $this->w - 2;
            } else {
                $flag = 'xRight';
                ($this->constantMotion) ? $this->x++ : $this->x;
            }
        } else {
            $flag = 'xLeft';
            $this->x--;
        }
        return $this->x;
    }

    private function checkTopBorder(&$flag)
    {
        if ($this->y == 1) {
            if ($this->liquid) {
                $flag = 'yUp';
                $this->y = $this->h - 2;
            } else {
                $flag = 'yDown';
                ($this->constantMotion) ? $this->y++ : $this->y;
            }
        } else {
            $flag = 'yUp';
            $this->y--;
        }
        return $this->y;
    }

    private function checkBottomBorder(&$flag)
    {
        if ($this->y == $this->h - 2) {
            if ($this->liquid) {
                $flag = 'yDown';
                $this->y = 1;
            } else {
                $flag = 'yUp';
                ($this->constantMotion) ? $this->y-- : $this->y;
            }
        } else {
            $flag = 'yDown';
            $this->y++;
        }
        return $this->y;
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
